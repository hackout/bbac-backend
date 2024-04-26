<?php
namespace App\Services\Backend;

use App\Models\Account;
use App\Models\User;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * 员工服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class UserService extends Service
{
    use ImportTemplateTrait, ExportTemplateTrait;

    public ?string $className = User::class;

    /**
     * 获取员工列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getList(array $data = []): array
    {
        if ($data) {
            $conditions = [];
            if (array_key_exists('gender', $data) && intval($data['gender'])) {
                $conditions['gender'] = $data['gender'];
            }
            if (array_key_exists('nation', $data) && intval($data['nation'])) {
                $conditions['nation'] = $data['nation'];
            }
            if (array_key_exists('skill_level', $data) && intval($data['skill_level'])) {
                $conditions['skill_level'] = $data['skill_level'];
            }
            if (array_key_exists('career_level', $data) && intval($data['career_level'])) {
                $conditions['career_level'] = $data['career_level'];
            }
            if (array_key_exists('keyword', $data) && trim($data['keyword'])) {
                $conditions['keyword'] = trim($data['keyword']);
            }
            if ($conditions) {
                $userIdList = ['-1'] + (new ProfileService)->getUserIdListByConditions($conditions);
                if (array_key_exists('keyword', $data) && trim($data['keyword'])) {
                    $userIdList += (new AccountService)->getUserIdListByConditions($data);
                }
                parent::setQuery(function ($query) use ($userIdList) {
                    $query->whereIn('id', $userIdList);
                });
            }
        }
        return parent::list([
            'id',
            'department_id',
            'department.name as department',
            'profile.name',
            'profile.gender',
            'username',
            'mobile',
            'number',
            'email',
            'is_valid',
            'is_lock',
            'is_super',
            'roles:name,id'
        ]);
    }

    /**
     * 设置员工状态
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $slug
     * @param  array  $data
     * @return void
     */
    public function patch(string $slug, array $data = [])
    {
        $where = [
            [
                function ($query) use ($data) {
                    $query->whereIn('id', $data['ids']);
                }
            ]
        ];
        switch ($slug) {
            case 'invalid':
                $sql = ['is_valid' => false];
                break;
            case 'valid':
                $sql = ['is_valid' => true];
                break;
            case 'lock':
                $sql = ['is_lock' => true];
                break;
            default:
                $sql = [
                    'has_error' => false,
                    'is_lock' => false,
                    'failed_count' => 0
                ];
                break;
        }
        parent::updateV2($where, $sql);
    }

    public function create(array $data): bool
    {
        $sql = [
            'password' => Hash::make((string) env('DEFAULT_PASSWORD')),
            'is_super' => false,
            'is_valid' => array_key_exists('is_valid', $data) ? (bool) $data['is_valid'] : true,
            'department_id' => array_key_exists('department_id', $data) ? (string) $data['department_id'] : null,
        ];
        $roles = array_key_exists('roles', $data) && $data['roles'] ? $data['roles'] : [];
        $username = array_key_exists('username', $data) && $data['username'] ? trim($data['username']) : null;
        $email = array_key_exists('email', $data) && $data['email'] ? trim($data['email']) : null;
        $number = array_key_exists('number', $data) && $data['number'] ? trim($data['number']) : null;
        $mobile = array_key_exists('mobile', $data) && $data['mobile'] ? trim($data['mobile']) : null;
        $result = parent::create($sql);
        if ($result) {
            $account = new AccountService;
            $username && $account->createAccount($this->item, $username, Account::ACCOUNT);
            $email && $account->createAccount($this->item, $email, Account::EMAIL);
            $number && $account->createAccount($this->item, $number, Account::NUMBER);
            $mobile && $account->createAccount($this->item, $mobile, Account::MOBILE);
            $roles && $this->item->roles()->attach($roles);
        }
        return $result;
    }

    public function update(int|string $id, array $data): bool
    {
        $sql = [
            'is_valid' => array_key_exists('is_valid', $data) ? (bool) $data['is_valid'] : true,
            'department_id' => array_key_exists('department_id', $data) ? (string) $data['department_id'] : null,
        ];
        $roles = array_key_exists('roles', $data) && $data['roles'] ? $data['roles'] : [];
        $result = parent::update($id, $sql);
        if ($result) {
            if ($roles) {
                $this->item->roles()->sync($roles);
            } else {
                $this->item->roles()->detach();
            }
        }
        return $result;
    }

    public function getMemberByDepartment(string $department_id): array
    {
        $this->setQuery(['department_id' => $department_id]);
        $this->setHas([
            'profile' => function ($query) {
                $query->whereNotNull('name');
            }
        ]);
        return parent::getAll([
            'id as value',
            'profile.name as name'
        ])->toArray();
    }


    /**
     * 获取生日列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getListOfBirthday(array $data): array
    {
        if ($data) {
            $conditions = [];
            if (array_key_exists('birth', $data) && intval($data['birth'])) {
                $conditions['birth'] = $data['birth'];
            }
            if (array_key_exists('birthday_card_id', $data) && intval($data['birthday_card_id'])) {
                $conditions['birthday_card_id'] = $data['birthday_card_id'];
            }
            if (array_key_exists('keyword', $data) && trim($data['keyword'])) {
                $conditions['keyword'] = trim($data['keyword']);
            }
            if ($conditions) {
                $userIdList = ['-1'] + (new ProfileService)->getUserIdListByConditions($conditions);
                if (array_key_exists('keyword', $data) && trim($data['keyword'])) {
                    $userIdList += (new AccountService)->getUserIdListByConditions($data);
                }
                parent::setQuery(function ($query) use ($userIdList) {
                    $query->whereIn('id', $userIdList);
                });
            }
        }
        $this->setHas([
            'profile' => function($query){
                $query->whereNotNull('user_id');
            }
        ]);
        return parent::list([
            'id',
            'profile.name',
            'profile.birth',
            'number',
            'profile.birthday_card_id',
        ]);
    }

    /**
     * 设置员工生日卡
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  array $userIdList
     * @return void
     */
    public function saveBirthdayCard(string $id, array $userIdList)
    {
        (new ProfileService)->setValue(function ($query) use ($userIdList) {
            $query->whereIn('user_id', $userIdList);
        }, 'birthday_card_id', $id);
    }

    /**
     * 获取员工列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return mixed
     */
    public function getOptions()
    {
        return parent::getAll()->map(function ($item) {
            return [
                'value' => $item->id,
                'name' => optional($item)->profile->name ?? $item->number,
                'email' => $item->email,
                'mobile' => $item->mobile,
                'number' => $item->number,
                'roles' => $item->roles->pluck('name')->toArray()
            ];
        });
    }

    /**
     * 获取Leader
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @return string
     */
    public function getLeaderByUser(User $user): string
    {
        if (!$user->department) {
            return $user->id;
        }
        return optional($user->department)->leader_id;
    }

    /**
     * 权限查看用户信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $user_id
     * @param  User   $user
     * @return array
     */
    public function getDetailByLeader(string $user_id, User $user): array
    {
        $item = parent::findById($user_id);
        if (!$user->departments || $user->departments->where('id', $item->department_id)->first()) {
            throw ValidationException::withMessages(['permission_incorrect' => '员工不存在']);
        }
        return (new ProfileService)->getProfileByUser($item);
    }

    /**
     * 修改用户完整资料
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $user_id
     * @param  User   $user
     * @param  array  $data
     * @return void
     */
    public function updateDetail(string $user_id, User $user, array $data)
    {
        $item = parent::findById($user_id);
        if (!$user->departments || $user->departments->where('id', $item->department_id)->first()) {
            throw ValidationException::withMessages(['permission_incorrect' => '员工不存在']);
        }
        $profile = [
            'name' => $data['name'],
            'pinyin' => $data['pinyin'],
            'gender' => $data['gender'],
            'birth' => $data['birth'],
            'nation' => $data['nation'],
            'birthplace' => $data['birthplace'],
            'address' => $data['address'],
            'id_card' => $data['id_card'],
            'educational' => $data['educational'],
            'science' => $data['science'],
            'emergency_contact' => $data['emergency_contact'],
            'emergency_telephone' => $data['emergency_telephone'],
            'vocational_skills' => $data['vocational_skills'],
            'skill_level' => $data['skill_level'],
            'career_level' => $data['career_level'],
            'attend_date' => $data['attend_date'],
            'entry_date' => $data['entry_date'],
        ];
        $username = trim($data['username']);
        $mobile = trim($data['mobile']);
        $number = trim($data['number']);
        $email = trim($data['email']);
        $sql = [
            'department_id' => trim($data['department_id']),
        ];
        $roles = (array) $data['roles'];
        if ((new ProfileService)->updateOrCreate($item, $profile)) {
            $username && (new AccountService)->updateOrCreate($item, Account::ACCOUNT, $username);
            $mobile && (new AccountService)->updateOrCreate($item, Account::MOBILE, $mobile);
            $email && (new AccountService)->updateOrCreate($item, Account::EMAIL, $email);
            $number && (new AccountService)->updateOrCreate($item, Account::NUMBER, $number);
            if (parent::update($item->id, $sql)) {
                $item->roles()->sync($roles);
            }
        }
    }

}