<?php

namespace App\Observers;

use App\Models\Role;

class RoleObserver
{

    /**
     * 角色删除后
     */
    public function deleted(Role $role): void
    {
        /**
         * 移除用户关联
         */
        $role->users && $role->users()->detach();
    }

}
