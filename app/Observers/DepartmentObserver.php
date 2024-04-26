<?php

namespace App\Observers;

use App\Models\Department;
use App\Services\Private\DepartmentService;

class DepartmentObserver
{


    /**
     * Handle the Department "saved" event.
     */
    public function saved(Department $department): void
    {
        if($department->getOriginal('role') != $department->role)
        {
            (new DepartmentService)->syncRole($department);
        }
    }
    /**
     * 角色删除后
     */
    public function deleted(Department $department): void
    {
        /**
         * 移除用户关联
         */
        $department->users && $department->users()->update(['department_id'=>null]);

        /**
         * 存在下级则删除下级
         */
        $department->children && $department->children->each(fn($child) => $child->delete());
    }

}
