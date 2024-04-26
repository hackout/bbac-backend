<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = $this->getList();
        foreach($list as $rs)
        {
            Role::create($rs);
        }
    }

    private function getList()
    {
        return [
            [
                'name' => '部门管理员'
            ],
            [
                'name' => '产品工程师'
            ],
            [
                'name' => '整车工程师'
            ],
            [
                'name' => '在线工程师'
            ],
            [
                'name' => '产品班长'
            ],
            [
                'name' => '整车班长'
            ],
            [
                'name' => '在线班长'
            ],
            [
                'name' => '蓝领'
            ]
        ];
    }
}
