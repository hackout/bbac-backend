<?php
namespace App\Services\Frontend;

use App\Models\Account;
use App\Models\Profile;
use App\Models\TrainingUser;
use App\Models\User;
use App\Packages\ImagePlus\ImagePlus;
use Carbon\Carbon;
use App\Services\Service;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 员工服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class UserService extends Service
{

    public ?string $className = User::class;

    /**
     * 获取个人资料
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getProfile(User $user): array
    {
        return [
            'item' => [
                'username' => $user->username,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'number' => $user->number,
                'name' => optional($user->profile)->name,
                'pinyin' => optional($user->profile)->pinyin,
                'avatar' => optional($user->profile)->avatar,
                'department' => optional($user->department)->name,
                'department_id' => $user->department_id,
                'roles' => $user->is_super ? ['超级管理员'] : $user->roles->map(fn($n) => $n->name),
                'roleList' => $user->is_super ? [] : $user->roles->map(fn($n) => $n->id)
            ],
            'profile' => $user->profile
        ];
    }

    /**
     * 获取会员首页信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @return array
     */
    public function getIndex(User $user): array
    {
        return [
            'name' => optional($user->profile)->name,
            'pinyin' => optional($user->profile)->pinyin,
            'skill_level' => optional($user->profile)->skill_level,
            'career_level' => optional($user->profile)->career_level,
            'vocational_skills' => optional($user->profile)->vocational_skills,
            'number' => $user->number,
            'avatar' => optional($user->profile)->avatar,
            'roles' => $user->roles->pluck('name')->toArray(),
            'department' => optional($user->department)->name,
            'birthday' => true
        ];
    }

    /**
     * 获取首页统计数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @return array
     */
    public function getStatistic(User $user): array
    {
        $allTrainings = (new TrainingService)->getAllValid($user);
        $result = [
            'safe' => [
                'total' => 0,
                'processing' => 0,
                'finish' => 0,
                'percent' => 0,
                'list' => []
            ],
            'skill' => [
                'total' => 0,
                'processing' => 0,
                'finish' => 0,
                'percent' => 0,
                'list' => []
            ],
            'multiple' => [
                'total' => 0,
                'processing' => 0,
                'finish' => 0,
                'percent' => 0,
                'list' => []
            ]
        ];
        foreach ($allTrainings as $type => $trainings) {
            foreach ($trainings as $training) {
                if ($training['status'] != TrainingUser::STATUS_NOT_INVOLVED) {
                    $result[$type]['total']++;
                    if ($training['status'] > TrainingUser::STATUS_NONPARTICIPATION) {
                        $result[$type]['finish']++;
                    } else {
                        $result[$type]['processing']++;
                    }
                    $result[$type]['list'][] = $training;
                }
            }
        }
        foreach ($result as $type => $rs) {
            if ($rs['total'] > 0) {
                $result[$type]['percent'] = $rs['processing'] > 0 ? ceil(bcdiv($rs['finish'], $rs['total'], 2) * 100) : 100;
            }
        }
        return $result;
    }

    /**
     * 更新用户头像
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User         $user
     * @param  UploadedFile $file
     * @return array
     */
    public function updateAvatar(User $user, UploadedFile $file): array
    {
        $user->profile->addMedia($file)->toMediaCollection(Profile::MEDIA_FILE);
        return [
            'name' => $user->profile->name,
            'avatar' => $user->profile->avatar
        ];
    }

    /**
     * 获取技能矩阵
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @return array
     */
    public function getSkill(User $user): array
    {
        $allTrainings = (new TrainingService)->getAllValid($user);
        $skills = collect($allTrainings['skill']);
        $result = [
            'total' => $skills->filter(fn($n) => $n['status'] != TrainingUser::STATUS_NOT_INVOLVED)->count(),
            'list' => $skills->filter(fn($n) => $n['status'] != TrainingUser::STATUS_NOT_INVOLVED)->values()
        ];
        return $result;
    }

    public function setting(User $user, array $data)
    {

        $email = (string) $data['email'];
        $mobile = (string) $data['mobile'];
        $number = (string) $data['number'];
        $username = (string) $data['username'];
        $profile = [
            'name' => (string) $data['name'],
            'pinyin' => trim($data['pinyin']) ? (string) $data['pinyin'] : Str::ucfirst(pinyin((string) $data['name'])),
            'gender' => (int) $data['gender'],
            'birth' => Carbon::parse($data['birth']),
            'nation' => (string) $data['nation'],
            'birthplace' => (string) $data['birthplace'],
            'address' => (string) $data['address'],
            'id_card' => (string) $data['id_card'],
            'educational' => (string) $data['educational'],
            'science' => (string) $data['science'],
            'emergency_contact' => (string) $data['emergency_contact'],
            'emergency_telephone' => (string) $data['emergency_telephone'],
            'skill_level' => (int) $data['skill_level'],
            'career_level' => (int) $data['career_level'],
            'vocational_skills' => (array) $data['vocational_skills'],
            'attend_date' => Carbon::parse($data['attend_date']),
            'entry_date' => Carbon::parse($data['entry_date']),
        ];
        (new ProfileService)->updateOrCreate($user, $profile);
        $accountService = new AccountService;
        $email != $user->email && $accountService->createAccount($user, $email, Account::EMAIL);
        $mobile != $user->mobile && $accountService->createAccount($user, $mobile, Account::MOBILE);
        $number != $user->number && $accountService->createAccount($user, $number, Account::NUMBER);
        $username != $user->username && $accountService->createAccount($user, $username, Account::ACCOUNT);
    }

    /**
     * 获取员工生日卡片
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User        $user
     * @return string|null
     */
    public function birth(User $user):?string
    {
        $result = null;
        if(!optional($user->profile)->birthday_card_id) return $result;
        if($user->profile->birth_image) return $user->profile->birth_image;
        $birthday_card = (new BirthdayCardService)->getTemplateData($user->profile->birthday_card_id);
        $imagePlus = new ImagePlus($birthday_card['template']);
        $cardPath = $imagePlus->makeBirthdayCard($user, $birthday_card['data']);
        if ($cardPath) {
            $result = (new ProfileService)->addBirthCard($user,$cardPath);
            @unlink($cardPath);
        }
        return $result;
    }
}