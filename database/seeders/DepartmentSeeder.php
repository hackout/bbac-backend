<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = $this->getList();
        foreach ($list as $rs) {
            $item = collect($rs);
            $sql = $item->only(['name', 'contact', 'mobile', 'email'])->toArray();
            if ($department = Department::create($sql)) {
                foreach ($rs['children'] as $rs1) {
                    $rs1['parent_id'] = $department->id;
                    $_item = collect($rs1);
                    $_sql = $_item->only(['name', 'contact', 'mobile', 'email', 'parent_id'])->toArray();
                    if ($_department = Department::create($_sql)) {
                        $_department->children()->createMany($_item['children']);
                    }
                }
            }
        }
    }

    private function getList()
    {
        return [
            [
                'name' => '北京奔驰发动机质量分析及问题解决科',
                'contact' => '刘二',
                'mobile' => '13888888888',
                'email' => 'sample@bbac.com.cn',
                'children' => [
                        [
                            'name' => '产品考核工段',
                            'contact' => '张三',
                            'mobile' => '13888888888',
                            'email' => 'sample@bbac.com.cn',
                            'children' => [
                                    [
                                        'name' => '产品考核班组',
                                        'contact' => '赵六',
                                        'mobile' => '13888888888',
                                        'email' => 'sample@bbac.com.cn',
                                    ]
                                ]
                        ],
                        [
                            'name' => '在线考核',
                            'contact' => '李四',
                            'mobile' => '13888888888',
                            'email' => 'sample@bbac.com.cn',
                            'children' => [
                                    [
                                        'name' => 'Z班组',
                                        'contact' => '何七',
                                        'mobile' => '13888888888',
                                        'email' => 'sample@bbac.com.cn',
                                    ]
                                ]
                        ],
                        [
                            'name' => '整车服务',
                            'contact' => '王五',
                            'mobile' => '13888888888',
                            'email' => 'sample@bbac.com.cn',
                            'children' => [
                                    [
                                        'name' => 'C班组',
                                        'contact' => '林八',
                                        'mobile' => '13888888888',
                                        'email' => 'sample@bbac.com.cn',
                                    ]
                                ]
                        ],
                    ]
            ]
        ];
    }
}
