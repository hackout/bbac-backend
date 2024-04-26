<?php
namespace App\Services\Private;

use App\Models\User;
use App\Services\Service;
use Illuminate\Support\Collection;

class UserService extends Service
{
    public ?string $className = User::class;

    public function getAllOptions():Collection
    {
        return parent::getAll([
            'number',
            'profile.name',
            'id'
        ])->pluck('name','number');
    }

    public function getAllId():array
    {
        return parent::getAll([
            'id'
        ])->pluck('id')->all();
    }

    public function getParents(User $user): array
    {
        $result = [];
        if (!$user->department) {
            return $result;
        }
        if (!$user->department->users->count()) {
            if ($user->department->parent && $user->department->parent->users->count()) {
                foreach ($user->department->parent->users as $u) {
                    $result[] = $u->id;
                }
            }
        } else {
            foreach ($user->department->users as $u) {
                $result[] = $u->id;
            }
        }
        return $result;
    }

    /**
     * 获取可导出数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $userIdList
     * @return array
     */
    public function getUserForExport(array $userIdList):array
    {
        parent::setQuery(function($query) use($userIdList){
            $query->whereIn('id',$userIdList);
        });
        $dictService = new DictService;
        $gender = $dictService->getOptionByCode('gender');
        $nation = $dictService->getOptionByCode('nation');
        $skill_level = $dictService->getOptionByCode('skill_level');
        $career_level = $dictService->getOptionByCode('career_level');
        return parent::getAll()->map(function($user) use($gender,$nation,$skill_level,$career_level){
            return [
                'number' => $user->number,
                'username' => $user->username,
                'name' => optional($user->profile)->name,
                'mobile' => $user->mobile,
                'email' => $user->email,
                'department' => optional($user->department)->name,
                'roles' => optional($user->roles)->pluck('name')->join('|'),
                'gender' => $gender->where('value',optional($user->profile)->gender)->value('name'),
                'birth' =>  optional(optional($user->profile)->birth)->toDateString(),
                'nation' => $nation->where('value',optional($user->profile)->nation)->value('name'),
                'birthplace' => optional($user->profile)->birthplace,
                'address' => optional($user->profile)->address,
                'id_card' => optional($user->profile)->id_card,
                'educational' => optional($user->profile)->educational,
                'science' => optional($user->profile)->science,
                'emergency_contact' => optional($user->profile)->emergency_contact,
                'emergency_telephone' => optional($user->profile)->emergency_telephone,
                'skill_level' => $skill_level->where('value',optional($user->profile)->skill_level)->value('name'),
                'career_level' => $career_level->where('value',optional($user->profile)->career_level)->value('name'),
                'attend_date' => optional(optional($user->profile)->attend_date)->toDateString(),
                'entry_date' => optional(optional($user->profile)->entry_date)->toDateString(),
                'lasted_login' => optional($user->lasted_login)->toDateTimeString(),
                'lasted_ip_address' => $user->lasted_ip_address,
                'is_valid' => $user->is_valid ? '正常' : '禁用',
                'created_at' => $user->created_at->toDateTimeString(),
            ];
        })->toArray();
    }

    /**
     * 通过ID获取选项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $userIdList
     * @return array
     */
    public function getOptionByIdList(array $userIdList):array
    {
        parent::setQuery(function($query) use($userIdList){
            $query->whereIn('id',$userIdList);
        });
        return parent::getAll()->map(function($item){
            return [
                'value' => $item->id,
                'name' => optional($item->profile)->name ?? $item->number
            ];
        })->toArray();
    }

}