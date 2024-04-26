<?php

namespace App\Packages\Department;

use App\Models\User;
use App\Models\Department as DepartmentModel;

class DepartmentRole
{

    /**
     * 在线考核权限
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return bool
     */
    public static function checkInline(User $user): bool
    {
        return $user->is_super || optional($user->department)->role == DepartmentModel::ROLE_INLINE;
    }

    /**
     * 产品考核权限
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return bool
     */
    public static function checkProduct(User $user): bool
    {
        return $user->is_super || optional($user->department)->role == DepartmentModel::ROLE_PRODUCT;
    }

    /**
     * 整车服务权限
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return bool
     */
    public static function checkVehicle(User $user): bool
    {
        return $user->is_super || optional($user->department)->role == DepartmentModel::ROLE_VEHICLE;
    }

    /**
     * 动态权限
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  int $type 类型
     * @param  User $user 用户
     * @return bool
     */
    public static function checkDynamic(int $type ,User $user): bool
    {
        return $user->is_super || optional($user->department)->role == $type;
    }
}