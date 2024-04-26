<?php
namespace App\Services\Private;

use App\Models\Department;
use App\Models\User;
use App\Services\Service;
use Illuminate\Database\Eloquent\Collection;

/**
 * 组织机构服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DepartmentService extends Service
{

    public ?string $className = Department::class;

    /**
     * 部门获取Leader
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Department $department
     * @return ?User
     */
    public function getLeaderByDepartment(Department $department): ?User
    {
        return $department->leader;
    }

    /**
     * 部门获取员工
     * 不包含Leader
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Department $department
     * @return ?Collection<User>
     */
    public function getUsersByDepartment(Department $department): Collection
    {
        return $department->users->filter(fn($user) => $user->id != $department->leader_id)->values();
    }

    /**
     * 获取所有用户
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Department $department
     * @return array
     */
    public function getAllUserByDepartment(Department $department): array
    {

        $userIdList = $department->users->pluck('id')->toArray();
        if($department->children)
        {
            foreach($department->children as $children)
            {
                $userIdList = array_merge($userIdList,$this->getAllUserByDepartment($children));
            }
        }
        return array_unique($userIdList);
    }

    /**
     * 员工获取Leader
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return ?User
     */
    public function getLeaderByUser(User $user): ?User
    {
        return optional($user->department)->leader;
    }

    /**
     * 员工获取同级员工
     * 不包含Leader
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $department
     * @return ?Collection<User>
     */
    public function getUsersByUser(User $user): Collection
    {
        return optional($user->department)->users->filter(fn($user) => $user->id != $department->leader_id)->values();
    }

    /**
     * 同步下级用户权限
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Department $department
     * @return void
     */
    public function syncRole(Department $department)
    {
        if($department->role > Department::ROLE_MANAGER)
        {
            $department->children->each(fn($child) => parent::update($child->id, ['role' => $department->role]));
        }
    }
}