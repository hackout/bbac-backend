<?php
namespace App\Services\Backend;

use App\Models\User;
use App\Models\Profile;
use App\Services\Service;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;

/**
 * 字典服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ProfileService extends Service
{
    public ?string $className = Profile::class;

    /**
     * 保存设置
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User|Authenticatable|null  $user
     * @param  array $data
     * @return bool
     */
    public function updateOrCreate(User|Authenticatable|null $user,array $data):bool
    {
        if(!$user) return false;
        $sql = [
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
            'skill_level' => $data['skill_level'],
            'career_level' => $data['career_level'],
            'vocational_skills' => $data['vocational_skills'],
            'attend_date' => $data['attend_date'],
            'entry_date' => $data['entry_date'],
        ];
        if($user->profile)
        {
            return parent::update($user->profile->id,$sql);
        }
        $sql['user_id'] = $user->id;
        return parent::create($sql);
    }

    public function setBirthdayCard(User $user,string $birthday_card_id = null):bool
    {
        if(!$user->profile)
        {
            throw ValidationException::withMessages(['profile.no_exists'=>'当前员工尚未完善资料']);
        }
        return parent::setValue($user->profile->id,'birthday_card_id',$birthday_card_id);
    }

    /**
     * 查询并获取用户ID
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array      $data
     * @return array|null
     */
    public function getUserIdListByConditions(array $data):?array
    {
        $conditions = [
            'gender' => 'eq',
            'nation' => 'eq',
            'skill_level' => 'eq',
            'career_level' => 'eq',
            'birth' => 'date',
            'birthday_card_id' => 'eq',
            'keyword' => [
                'search',
                [
                    'name',
                    'id_card',
                    'pinyin',
                    'birthplace',
                    'address',
                    'emergency_contact',
                    'emergency_telephone',
                    'account'
                ]
            ]
        ];
        parent::listQuery($data,$conditions);
        return parent::getAll(['user_id'])->pluck('user_id')->toArray();
    }


    public function getProfileByUser(mixed $user):array
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
                'is_super' => $user->is_super,
                'department' => optional($user->department)->name,
                'department_id' => $user->department_id,
                'roles' => $user->is_super ? ['超级管理员'] : $user->roles->map(fn($n)=>$n->name),
                'roleList' => $user->is_super ? [] : $user->roles->map(fn($n)=>$n->id)
            ],
            'profile' => $user->profile
        ];
    }
}