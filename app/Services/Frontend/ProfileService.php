<?php
namespace App\Services\Frontend;

use App\Models\User;
use App\Models\Profile;
use App\Services\Service;
use Illuminate\Contracts\Auth\Authenticatable;

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
            'name' => array_key_exists('name',$data) ? $data['name'] : null,
            'pinyin' => array_key_exists('pinyin',$data) ? $data['pinyin'] : null,
            'gender' => array_key_exists('gender',$data) ? $data['gender'] : null,
            'birth' => array_key_exists('birth',$data) ? $data['birth'] : null,
            'nation' => array_key_exists('nation',$data) ? $data['nation'] : null,
            'birthplace' => array_key_exists('birthplace',$data) ? $data['birthplace'] : null,
            'address' => array_key_exists('address',$data) ? $data['address'] : null,
            'id_card' => array_key_exists('id_card',$data) ? $data['id_card'] : null,
            'educational' => array_key_exists('educational',$data) ? $data['educational'] : null,
            'science' => array_key_exists('science',$data) ? $data['science'] : null,
            'emergency_contact' => array_key_exists('emergency_contact',$data) ? $data['emergency_contact'] : null,
            'emergency_telephone' => array_key_exists('emergency_telephone',$data) ? $data['emergency_telephone'] : null,
            'skill_level' => array_key_exists('skill_level',$data) ? $data['skill_level'] : null,
            'career_level' => array_key_exists('career_level',$data) ? $data['career_level'] : null,
            'attend_date' => array_key_exists('attend_date',$data) ? $data['attend_date'] : null,
            'entry_date' => array_key_exists('entry_date',$data) ? $data['entry_date'] : null,
            'vocational_skills' => array_key_exists('vocational_skills',$data) ? $data['vocational_skills'] : [],
        ];
        if($user->profile)
        {
            return parent::update($user->profile->id,$sql);
        }
        $sql['user_id'] = $user->id;
        return parent::create($sql);
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

    /**
     * 保存生日卡并返回地址
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User        $user
     * @param  [type]      $path
     * @return string|null
     */
    public function addBirthCard(User $user,$path):?string
    {
        $media = $user->profile->addMedia($path)->toMediaCollection(Profile::MEDIA_BIRTH);
        return $media->getUrl();
    }
}