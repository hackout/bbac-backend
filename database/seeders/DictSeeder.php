<?php

namespace Database\Seeders;

use App\Models\Dict;
use Illuminate\Database\Seeder;

class DictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = $this->getList();
        foreach ($list as $rs) {
            $sql = [
                'name' => $rs['name'],
                'code' => $rs['code'],
                'description' => array_key_exists('description', $rs) ? $rs['description'] : null
            ];
            if ($item = Dict::create($sql)) {
                $item->items()->createMany($rs['options']);
            }
        }
    }

    private function getList()
    {
        return [
            [
                'name' => '性别',
                'code' => 'gender',
                'options' => [
                    [
                        'name' => '先生',
                        'content' => 1
                    ],
                    [
                        'name' => '女士',
                        'content' => 2
                    ],
                    [
                        'name' => '其他',
                        'content' => 0
                    ]
                ]
            ],
            [
                'name' => '民族',
                'code' => 'nation',
                'options' => [
                    [
                        'name' => "汉族",
                        'content' => 0
                    ],
                    [
                        'name' => "蒙古族",
                        'content' => 1
                    ],
                    [
                        'name' => "回族",
                        'content' => 2
                    ],
                    [
                        'name' => "藏族",
                        'content' => 3
                    ],
                    [
                        'name' => "维吾尔族",
                        'content' => 4
                    ],
                    [
                        'name' => "苗族",
                        'content' => 5
                    ],
                    [
                        'name' => "彝族",
                        'content' => 6
                    ],
                    [
                        'name' => "壮族",
                        'content' => 7
                    ],
                    [
                        'name' => "布依族",
                        'content' => 8
                    ],
                    [
                        'name' => "朝鲜族",
                        'content' => 9
                    ],
                    [
                        'name' => "满族",
                        'content' => 10
                    ],
                    [
                        'name' => "侗族",
                        'content' => 11
                    ],
                    [
                        'name' => "瑶族",
                        'content' => 12
                    ],
                    [
                        'name' => "白族",
                        'content' => 13
                    ],
                    [
                        'name' => "土家族",
                        'content' => 14
                    ],
                    [
                        'name' => "哈尼族",
                        'content' => 15
                    ],
                    [
                        'name' => "哈萨克族",
                        'content' => 16
                    ],
                    [
                        'name' => "傣族",
                        'content' => 17
                    ],
                    [
                        'name' => "黎族",
                        'content' => 18
                    ],
                    [
                        'name' => "傈僳族",
                        'content' => 19
                    ],
                    [
                        'name' => "佤族",
                        'content' => 20
                    ],
                    [
                        'name' => "畲族",
                        'content' => 21
                    ],
                    [
                        'name' => "高山族",
                        'content' => 22
                    ],
                    [
                        'name' => "拉祜族",
                        'content' => 23
                    ],
                    [
                        'name' => "水族",
                        'content' => 24
                    ],
                    [
                        'name' => "东乡族",
                        'content' => 25
                    ],
                    [
                        'name' => "纳西族",
                        'content' => 26
                    ],
                    [
                        'name' => "景颇族",
                        'content' => 27
                    ],
                    [
                        'name' => "柯尔克孜族",
                        'content' => 28
                    ],
                    [
                        'name' => "土族",
                        'content' => 29
                    ],
                    [
                        'name' => "达斡尔族",
                        'content' => 30
                    ],
                    [
                        'name' => "仫佬族",
                        'content' => 31
                    ],
                    [
                        'name' => "羌族",
                        'content' => 32
                    ],
                    [
                        'name' => "布朗族",
                        'content' => 33
                    ],
                    [
                        'name' => "撒拉族",
                        'content' => 34
                    ],
                    [
                        'name' => "毛南族",
                        'content' => 35
                    ],
                    [
                        'name' => "仡佬族",
                        'content' => 36
                    ],
                    [
                        'name' => "锡伯族",
                        'content' => 37
                    ],
                    [
                        'name' => "阿昌族",
                        'content' => 38
                    ],
                    [
                        'name' => "普米族",
                        'content' => 39
                    ],
                    [
                        'name' => "塔吉克族",
                        'content' => 40
                    ],
                    [
                        'name' => "怒族",
                        'content' => 41
                    ],
                    [
                        'name' => "乌孜别克族",
                        'content' => 42
                    ],
                    [
                        'name' => "俄罗斯族",
                        'content' => 43
                    ],
                    [
                        'name' => "鄂温克族",
                        'content' => 44
                    ],
                    [
                        'name' => "德昂族",
                        'content' => 45
                    ],
                    [
                        'name' => "保安族",
                        'content' => 46
                    ],
                    [
                        'name' => "裕固族",
                        'content' => 47
                    ],
                    [
                        'name' => "京族",
                        'content' => 48
                    ],
                    [
                        'name' => "塔塔尔族",
                        'content' => 49
                    ],
                    [
                        'name' => "独龙族",
                        'content' => 50
                    ],
                    [
                        'name' => "鄂伦春族",
                        'content' => 51
                    ],
                    [
                        'name' => "赫哲族",
                        'content' => 52
                    ],
                    [
                        'name' => "门巴族",
                        'content' => 53
                    ],
                    [
                        'name' => "珞巴族",
                        'content' => 54
                    ],
                    [
                        'name' => "基诺族",
                        'content' => 55
                    ],
                    [
                        'name' => "其他",
                        'content' => 56
                    ]
                ]
            ],
            [
                'name' => '职业等级',
                'code' => 'career_level',
                'options' => [
                    [
                        'name' => '初级工',
                        'content' => 1
                    ],
                    [
                        'name' => '中级工',
                        'content' => 2
                    ],
                    [
                        'name' => '高级工',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '综合技能等级',
                'code' => 'skill_level',
                'options' => [
                    [
                        'name' => 'Level 1',
                        'content' => 1
                    ],
                    [
                        'name' => 'Level 2',
                        'content' => 2
                    ],
                    [
                        'name' => 'Level 3',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '工厂类型',
                'code' => 'plant',
                'options' => [
                    [
                        'name' => 'EP1',
                        'content' => 1
                    ],
                    [
                        'name' => 'EP2',
                        'content' => 2
                    ]
                ]
            ],
            [
                'name' => '生产线',
                'code' => 'assembly_line',
                'options' => [
                    [
                        'name' => 'L1',
                        'content' => 1
                    ],
                    [
                        'name' => 'L2',
                        'content' => 2
                    ],
                    [
                        'name' => 'L3',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '发动机机型',
                'code' => 'engine_type',
                'options' => [
                    [
                        'name' => 'M254',
                        'content' => 1
                    ],
                    [
                        'name' => 'M260',
                        'content' => 2
                    ],
                    [
                        'name' => 'M274',
                        'content' => 3
                    ],
                    [
                        'name' => 'M264',
                        'content' => 4
                    ],
                    [
                        'name' => 'M282',
                        'content' => 5
                    ],
                ]
            ],
            [
                'name' => '车型',
                'code' => 'motorcycle_type',
                'options' => [
                    [
                        'name' => 'C200L',
                        'content' => 1
                    ],
                    [
                        'name' => 'C260L',
                        'content' => 2
                    ],
                    [
                        'name' => 'C350eL',
                        'content' => 3
                    ],
                    [
                        'name' => 'E260L',
                        'content' => 4
                    ],
                    [
                        'name' => 'E300L',
                        'content' => 5
                    ],
                    [
                        'name' => 'A180L',
                        'content' => 6
                    ],
                    [
                        'name' => 'A200L',
                        'content' => 7
                    ],
                    [
                        'name' => 'A220L',
                        'content' => 8
                    ],
                    [
                        'name' => 'A35L',
                        'content' => 9
                    ],
                    [
                        'name' => 'GLC260L',
                        'content' => 10
                    ],
                    [
                        'name' => 'EQE',
                        'content' => 11
                    ],
                    [
                        'name' => 'EQB',
                        'content' => 12
                    ],
                    [
                        'name' => 'EQA',
                        'content' => 13
                    ],
                    [
                        'name' => 'GLB200',
                        'content' => 14
                    ],
                    [
                        'name' => 'GLB220',
                        'content' => 15
                    ],
                    [
                        'name' => 'GLA200',
                        'content' => 16
                    ],
                    [
                        'name' => 'GLC300L',
                        'content' => 17
                    ],
                    [
                        'name' => 'GLA220',
                        'content' => 18
                    ]
                ]
            ],
            [
                'name' => '车系',
                'code' => 'car_series',
                'options' => [
                    [
                        'name' => 'V206',
                        'content' => 1
                    ],
                    [
                        'name' => 'V214',
                        'content' => 2
                    ],
                    [
                        'name' => 'Z177',
                        'content' => 3
                    ],
                    [
                        'name' => 'V254',
                        'content' => 4
                    ],
                    [
                        'name' => 'X243',
                        'content' => 5
                    ],
                    [
                        'name' => 'X247',
                        'content' => 6
                    ],
                    [
                        'name' => 'N293',
                        'content' => 7
                    ],
                    [
                        'name' => 'V295',
                        'content' => 8
                    ],
                    [
                        'name' => 'V294',
                        'content' => 9
                    ]
                ]
            ],
            [
                'name' => '项目阶段',
                'code' => 'assembly_status',
                'description' => '项目阶段列表',
                'options' => [
                    [
                        'name' => 'SOP',
                        'content' => 1
                    ],
                    [
                        'name' => 'PT1',
                        'content' => 2
                    ],
                    [
                        'name' => 'PT2',
                        'content' => 3
                    ],
                    [
                        'name' => 'PT3',
                        'content' => 4
                    ],
                    [
                        'name' => 'ET1',
                        'content' => 5
                    ],
                    [
                        'name' => 'ET2',
                        'content' => 6
                    ],
                    [
                        'name' => 'OT1',
                        'content' => 7
                    ],
                    [
                        'name' => 'OT2',
                        'content' => 8
                    ],
                    [
                        'name' => 'PerfT1',
                        'content' => 9
                    ],
                    [
                        'name' => 'PerfT2',
                        'content' => 10
                    ],
                    [
                        'name' => 'PerfT3',
                        'content' => 11
                    ]
                ]
            ],
            [
                'name' => '消息通知类型',
                'code' => 'notice_type',
                'options' => [
                    [
                        'name' => '信息分享',
                        'content' => 1
                    ],
                    [
                        'name' => '消息通知',
                        'content' => 2
                    ],
                    [
                        'name' => '工艺变更',
                        'content' => 3
                    ],
                    [
                        'name' => '任务通知',
                        'content' => 4
                    ],
                    [
                        'name' => '审批通知',
                        'content' => 5
                    ],
                ]
            ],
            [
                'name' => '培训类型',
                'code' => 'training_type',
                'options' => [
                    [
                        'name' => '安全培训',
                        'content' => 1
                    ],
                    [
                        'name' => '技能培训',
                        'content' => 2
                    ],
                    [
                        'name' => '综合培训',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '培训状态',
                'code' => 'training_status',
                'options' => [
                    [
                        'name' => '未开始',
                        'content' => 1
                    ],
                    [
                        'name' => '进行中',
                        'content' => 2
                    ],
                    [
                        'name' => '已完成',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '安全培训状态',
                'code' => 'training_1_status',
                'options' => [
                    [
                        'name' => '未参与',
                        'content' => 1
                    ],
                    [
                        'name' => '参与并通过考试',
                        'content' => 2
                    ],
                    [
                        'name' => '参与未通过考试',
                        'content' => 3
                    ],
                    [
                        'name' => '不涉及',
                        'content' => 4
                    ]
                ]
            ],
            [
                'name' => '技能培训状态',
                'code' => 'training_2_status',
                'options' => [
                    [
                        'name' => '0%',
                        'content' => 1
                    ],
                    [
                        'name' => '25%',
                        'content' => 2
                    ],
                    [
                        'name' => '50%',
                        'content' => 3
                    ],
                    [
                        'name' => '75%',
                        'content' => 5
                    ],
                    [
                        'name' => '100%',
                        'content' => 6
                    ],
                    [
                        'name' => '不涉及',
                        'content' => 4
                    ],
                ]
            ],
            [
                'name' => '其他培训状态',
                'code' => 'training_3_status',
                'options' => [
                    [
                        'name' => '未参与',
                        'content' => 1
                    ],
                    [
                        'name' => '参与并通过考试',
                        'content' => 2
                    ],
                    [
                        'name' => '参与未通过考试',
                        'content' => 3
                    ],
                    [
                        'name' => '不涉及',
                        'content' => 4
                    ]
                ]
            ],
            [
                'name' => '在线考核-考核类型',
                'code' => 'examine_inline_type',
                'options' => [
                    [
                        'name' => '常规考核',
                        'content' => 1
                    ],
                    [
                        'name' => '涂胶考核',
                        'content' => 2
                    ],
                    [
                        'name' => '动态考核',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '产品考核-考核类型',
                'code' => 'examine_product_type',
                'options' => [
                    [
                        'name' => '动态考核',
                        'content' => 1
                    ],
                    [
                        'name' => '拆检考核',
                        'content' => 2
                    ],
                    [
                        'name' => '装配考核',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '整车服务-考核项定义',
                'code' => 'examine_vehicle_item_type',
                'options' => [
                    [
                        'name' => '扭矩监控',
                        'content' => 1
                    ],
                    [
                        'name' => '尺寸测量',
                        'content' => 2
                    ],
                    [
                        'name' => '外观检测',
                        'content' => 3
                    ],
                    [
                        'name' => '过程监控',
                        'content' => 4
                    ],
                    [
                        'name' => '墨水测试',
                        'content' => 5
                    ],
                    [
                        'name' => '撕胶测试',
                        'content' => 6
                    ],
                    [
                        'name' => '触发考核',
                        'content' => 7
                    ],
                    [
                        'name' => '试装支持',
                        'content' => 8
                    ],
                    [
                        'name' => '项目支持',
                        'content' => 9
                    ],
                    [
                        'name' => '测量检查',
                        'content' => 10
                    ],
                    [
                        'name' => '目视检查',
                        'content' => 11
                    ]
                ]
            ],
            [
                'name' => '在线考核-考核项定义',
                'code' => 'examine_inline_item_type',
                'options' => [
                    [
                        'name' => '扭矩监控',
                        'content' => 1
                    ],
                    [
                        'name' => '尺寸测量',
                        'content' => 2
                    ],
                    [
                        'name' => '外观检测',
                        'content' => 3
                    ],
                    [
                        'name' => '过程监控',
                        'content' => 4
                    ],
                    [
                        'name' => '墨水测试',
                        'content' => 5
                    ],
                    [
                        'name' => '撕胶测试',
                        'content' => 6
                    ],
                    [
                        'name' => '触发考核',
                        'content' => 7
                    ],
                    [
                        'name' => '试装支持',
                        'content' => 8
                    ],
                    [
                        'name' => '项目支持',
                        'content' => 9
                    ]
                ]
            ],
            [
                'name' => '产品考核-考核项定义',
                'code' => 'examine_product_item_type',
                'options' => [
                    [
                        'name' => '测量检查',
                        'content' => 1
                    ],
                    [
                        'name' => '目视检查',
                        'content' => 2
                    ],
                    [
                        'name' => '全部',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '特殊特性',
                'code' => 'special',
                'description' => '特殊特性类型',
                'options' => [
                    [
                        'name' => 'DS',
                        'content' => 1
                    ],
                    [
                        'name' => 'DZ',
                        'content' => 2
                    ],
                    [
                        'name' => 'P',
                        'content' => 3
                    ],
                ]
            ],
            [
                'name' => '审核状态',
                'code' => 'approve_status',
                'options' => [
                    [
                        'name' => '待审核',
                        'content' => 0
                    ],
                    [
                        'name' => '通过',
                        'content' => 1
                    ],
                    [
                        'name' => '拒绝',
                        'content' => 2
                    ],
                ]
            ],
            [
                'name' => '考核模板状态',
                'code' => 'template_status',
                'options' => [
                    [
                        'name' => '待审核',
                        'content' => 0
                    ],
                    [
                        'name' => '审核中',
                        'content' => 1
                    ],
                    [
                        'name' => '审核通过',
                        'content' => 2
                    ],
                    [
                        'name' => '审核拒绝',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '排班考核状态',
                'code' => 'task_status',
                'options' => [
                    [
                        'name' => '未分配',
                        'content' => 0
                    ],
                    [
                        'name' => '已排班',
                        'content' => 1
                    ],
                    [
                        'name' => '进行中',
                        'content' => 2
                    ],
                    [
                        'name' => '已完成',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '螺栓-种类',
                'code' => 'bolt_model',
                'options' => [
                    [
                        'name' => 'A',
                        'content' => 1
                    ],
                    [
                        'name' => 'B',
                        'content' => 2
                    ],
                    [
                        'name' => 'C',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '螺栓-类型',
                'code' => 'bolt_type',
                'options' => [
                    [
                        'name' => '标准',
                        'content' => 1
                    ],
                    [
                        'name' => '自攻',
                        'content' => 2
                    ],
                    [
                        'name' => '带胶',
                        'content' => 3
                    ],
                    [
                        'name' => '其他',
                        'content' => 4
                    ]
                ]
            ],
            [
                'name' => '螺栓-放行状态',
                'code' => 'bolt_status',
                'options' => [
                    [
                        'name' => 'No Release',
                        'content' => 1
                    ],
                    [
                        'name' => 'P-PFU',
                        'content' => 2
                    ],
                    [
                        'name' => 'L-PFU',
                        'content' => 3
                    ],
                    [
                        'name' => 'P-Data Enough',
                        'content' => 4
                    ],
                    [
                        'name' => 'L-Data Enough',
                        'content' => 5
                    ],
                    [
                        'name' => 'P-To be confirmed',
                        'content' => 6
                    ],
                    [
                        'name' => 'L-To be confirmed',
                        'content' => 7
                    ],
                    [
                        'name' => 'R-P-PFU',
                        'content' => 8
                    ],
                    [
                        'name' => 'R-L-PFU',
                        'content' => 9
                    ]
                ]
            ],
            [
                'name' => '在线考核-单号',
                'code' => 'inline_order_rules',
                'options' => [
                    [
                        'name' => 'RY-{YMD}-{Assembly}-{Engine}',
                        'content' => 1
                    ],
                    [
                        'name' => 'IK-{YMD}-{Assembly}-{Engine}',
                        'content' => 2
                    ],
                    [
                        'name' => 'DY-{YMD}-{Assembly}-{Engine}',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '产品考核-单号',
                'code' => 'product_order_rules',
                'options' => [
                    [
                        'name' => 'AS-{YMD}-{Engine}',
                        'content' => 1
                    ],
                    [
                        'name' => 'PA-{YMD}-{Engine}',
                        'content' => 2
                    ],
                    [
                        'name' => 'DY-{YMD}-{Engine}',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '整车考核-单号',
                'code' => 'service_order_rules',
                'options' => [
                    [
                        'name' => 'DY-{YMD}-{Engine}',
                        'content' => 0
                    ]
                ]
            ],
            [
                'name' => '考核单类型',
                'code' => 'examine_type',
                'options' => [
                    [
                        'name' => '在线考核',
                        'content' => 1
                    ],
                    [
                        'name' => '产品部门',
                        'content' => 2
                    ],
                    [
                        'name' => '整车服务',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '考勤任务类型',
                'code' => 'work_type',
                'options' => [
                    [
                        'name' => '正常考核工时',
                        'content' => 1
                    ],
                    [
                        'name' => '动态考核',
                        'content' => 2
                    ],
                    [
                        'name' => '其他安排',
                        'content' => 3
                    ],
                    [
                        'name' => 'Campaign',
                        'content' => 4
                    ],
                    [
                        'name' => '培训',
                        'content' => 5
                    ],
                    [
                        'name' => '加班',
                        'content' => 6
                    ],
                    [
                        'name' => '休假',
                        'content' => 7
                    ]
                ]
            ],
            [
                'name' => '考核工时状态',
                'code' => 'period_status',
                'options' => [
                    [
                        'name' => '未开始',
                        'content' => 0
                    ],
                    [
                        'name' => '进行中',
                        'content' => 1
                    ],
                    [
                        'name' => '提前结束',
                        'content' => 2
                    ],
                    [
                        'name' => '已超时',
                        'content' => 3
                    ],
                    [
                        'name' => '超时结束',
                        'content' => 4
                    ]
                ]
            ],
            [
                'name' => '考核状态',
                'code' => 'examine_status',
                'options' => [
                    [
                        'name' => '未开始',
                        'content' => 0
                    ],
                    [
                        'name' => '进行中',
                        'content' => 1
                    ],
                    [
                        'name' => '已完成',
                        'content' => 2
                    ]
                ]
            ],
            [
                'name' => '考核项状态',
                'code' => 'examine_item_status',
                'options' => [
                    [
                        'name' => '未考核',
                        'content' => 0
                    ],
                    [
                        'name' => 'OK',
                        'content' => 1
                    ],
                    [
                        'name' => 'NOK',
                        'content' => 2
                    ]
                ]
            ],
            [
                'name' => '缺陷等级',
                'code' => 'defect_level',
                'options' => [
                    [
                        'name' => 'Prio1',
                        'content' => 1
                    ],
                    [
                        'name' => 'Prio2',
                        'content' => 2
                    ],
                    [
                        'name' => 'Prio3',
                        'content' => 3
                    ],
                    [
                        'name' => 'Prio4',
                        'content' => 4
                    ]
                ]
            ],
            [
                'name' => '缺陷分类',
                'code' => 'defect_category',
                'options' => [
                    [
                        'name' => '刮伤',
                        'content' => 1
                    ],
                    [
                        'name' => '过热',
                        'content' => 2
                    ],
                    [
                        'name' => '泄漏',
                        'content' => 3
                    ],
                    [
                        'name' => '磕痕',
                        'content' => 4
                    ],
                    [
                        'name' => '缺少材料',
                        'content' => 5
                    ],
                    [
                        'name' => '毛刺',
                        'content' => 6
                    ],
                    [
                        'name' => '损坏',
                        'content' => 7
                    ],
                    [
                        'name' => '变形',
                        'content' => 8
                    ],
                    [
                        'name' => '没有硅胶附着',
                        'content' => 9
                    ],
                    [
                        'name' => '间隙低于标准',
                        'content' => 10
                    ],
                    [
                        'name' => '异常的位置',
                        'content' => 11
                    ],
                    [
                        'name' => '没有被加工',
                        'content' => 12
                    ],
                    [
                        'name' => '铝片',
                        'content' => 13
                    ],
                    [
                        'name' => '没有装配到最后的位置',
                        'content' => 14
                    ],
                    [
                        'name' => '凹痕',
                        'content' => 15
                    ],
                    [
                        'name' => '冷却液渗漏',
                        'content' => 16
                    ],
                    [
                        'name' => '颜色标记脱落',
                        'content' => 17
                    ],
                    [
                        'name' => '装配错误',
                        'content' => 18
                    ],
                    [
                        'name' => '冷却液泄漏',
                        'content' => 19
                    ],
                    [
                        'name' => 'o形圈损坏',
                        'content' => 20
                    ],
                    [
                        'name' => '不正常的加工刀纹',
                        'content' => 21
                    ],
                    [
                        'name' => '轴向间隙小',
                        'content' => 22
                    ],
                    [
                        'name' => '标签被损坏',
                        'content' => 23
                    ],
                    [
                        'name' => '砂眼',
                        'content' => 24
                    ],
                    [
                        'name' => '从喷油器压力传感器插头上脱落',
                        'content' => 25
                    ],
                    [
                        'name' => '硅胶异常状态',
                        'content' => 26
                    ],
                    [
                        'name' => '乳化液',
                        'content' => 27
                    ],
                    [
                        'name' => '异常的刮痕',
                        'content' => 28
                    ],
                    [
                        'name' => '存储记录与实际不符的数据',
                        'content' => 29
                    ],
                    [
                        'name' => '硅胶附着状态异常',
                        'content' => 30
                    ],
                    [
                        'name' => '密封圈脱落',
                        'content' => 31
                    ],
                    [
                        'name' => '异物',
                        'content' => 32
                    ],
                    [
                        'name' => '变形',
                        'content' => 33
                    ],
                    [
                        'name' => '生锈',
                        'content' => 34
                    ],
                    [
                        'name' => '磨损',
                        'content' => 35
                    ],
                    [
                        'name' => '卡滞',
                        'content' => 36
                    ],
                    [
                        'name' => '松脱',
                        'content' => 37
                    ],
                    [
                        'name' => '其他',
                        'content' => 38
                    ]
                ]
            ],
            [
                'name' => '问题零件',
                'code' => 'problem_parts',
                'options' => [
                    [
                        'name' => 'Inter cooler',
                        'content' => 1
                    ],
                    [
                        'name' => 'VPD data',
                        'content' => 2
                    ],
                    [
                        'name' => 'Screw thread',
                        'content' => 3
                    ],
                    [
                        'name' => 'Purge line',
                        'content' => 4
                    ],
                    [
                        'name' => 'Front sealing',
                        'content' => 5
                    ],
                    [
                        'name' => 'Wiring harness',
                        'content' => 6
                    ],
                    [
                        'name' => 'Water pump',
                        'content' => 7
                    ],
                    [
                        'name' => 'Spark plug',
                        'content' => 8
                    ],
                    [
                        'name' => 'Oil suction pipe',
                        'content' => 9
                    ],
                    [
                        'name' => 'Fuel injector',
                        'content' => 10
                    ],
                    [
                        'name' => 'Turbocharger',
                        'content' => 11
                    ],
                    [
                        'name' => 'CS',
                        'content' => 12
                    ],
                    [
                        'name' => 'Timing chain',
                        'content' => 13
                    ],
                    [
                        'name' => 'Roller cam follower',
                        'content' => 14
                    ],
                    [
                        'name' => 'Camshaft',
                        'content' => 15
                    ],
                    [
                        'name' => 'CH',
                        'content' => 16
                    ],
                    [
                        'name' => 'Con-rod bearing',
                        'content' => 17
                    ],
                    [
                        'name' => 'Main bearing',
                        'content' => 18
                    ],
                    [
                        'name' => 'Piston pin',
                        'content' => 19
                    ],
                    [
                        'name' => 'Piston skirt',
                        'content' => 20
                    ],
                    [
                        'name' => 'Con-rod',
                        'content' => 21
                    ],
                    [
                        'name' => 'CB',
                        'content' => 22
                    ]
                ]
            ],
            [
                'name' => '问题位置',
                'code' => 'question_position',
                'options' => [
                    [
                        'name' => 'surface',
                        'content' => 1
                    ],
                    [
                        'name' => 'bottom',
                        'content' => 2
                    ],
                    [
                        'name' => 'upper side',
                        'content' => 3
                    ],
                    [
                        'name' => 'lower side',
                        'content' => 4
                    ],
                    [
                        'name' => 'terminal',
                        'content' => 5
                    ],
                    [
                        'name' => 'inside',
                        'content' => 6
                    ]
                ]
            ],
            [
                'name' => '具体位置 Inter cooler',
                'code' => 'exactly_1',
                'options' => [
                    [
                        'name' => 'Intake pipe heat shielding',
                        'content' => 1
                    ],
                    [
                        'name' => 'Intake pipe',
                        'content' => 2
                    ],
                    [
                        'name' => 'Throttle valve',
                        'content' => 3
                    ],
                    [
                        'name' => 'Air filter',
                        'content' => 4
                    ]
                ]
            ],
            [
                'name' => '具体位置 VPD data',
                'code' => 'exactly_2',
                'options' => [
                    [
                        'name' => 'VPD data',
                        'content' => 1
                    ]
                ]
            ],
            [
                'name' => '具体位置 Screw thread',
                'code' => 'exactly_3',
                'options' => [
                    [
                        'name' => 'Screw Mounting hole',
                        'content' => 1
                    ]
                ]
            ],
            [
                'name' => '具体位置 Purge line',
                'code' => 'exactly_4',
                'options' => [
                    [
                        'name' => 'Intake pipe',
                        'content' => 1
                    ],
                    [
                        'name' => 'Oil feed line',
                        'content' => 2
                    ],
                    [
                        'name' => 'Oil return line',
                        'content' => 3
                    ],
                    [
                        'name' => 'Water feed line',
                        'content' => 4
                    ],
                    [
                        'name' => 'Water return line',
                        'content' => 5
                    ]
                ]
            ],
            [
                'name' => '具体位置 Front sealing',
                'code' => 'exactly_5',
                'options' => [
                    [
                        'name' => 'Rear sealing',
                        'content' => 1
                    ],
                    [
                        'name' => 'Sealing ring',
                        'content' => 2
                    ],
                    [
                        'name' => 'O-ring',
                        'content' => 3
                    ],
                    [
                        'name' => 'gluing area',
                        'content' => 4
                    ]
                ]
            ],
            [
                'name' => '具体位置 Wiring harness',
                'code' => 'exactly_6',
                'options' => [
                    [
                        'name' => 'Oil level sensor',
                        'content' => 1
                    ],
                    [
                        'name' => 'Camshaft position sensor',
                        'content' => 2
                    ],
                    [
                        'name' => 'Fuel distributor',
                        'content' => 3
                    ],
                    [
                        'name' => 'Lift solenoid',
                        'content' => 4
                    ],
                    [
                        'name' => 'Ignition coil',
                        'content' => 5
                    ],
                    [
                        'name' => 'CS  Position sensor',
                        'content' => 6
                    ],
                    [
                        'name' => 'Exhaust gas turbocharger',
                        'content' => 7
                    ],
                    [
                        'name' => 'Throttle valve',
                        'content' => 8
                    ],
                    [
                        'name' => 'Small coolant pump',
                        'content' => 9
                    ],
                    [
                        'name' => 'Intake air pressure sensor',
                        'content' => 10
                    ],
                    [
                        'name' => 'Air filter',
                        'content' => 11
                    ],
                    [
                        'name' => 'Coolant pump',
                        'content' => 12
                    ],
                    [
                        'name' => 'Water temperature sensor',
                        'content' => 13
                    ],
                    [
                        'name' => 'Wiring harness plug',
                        'content' => 14
                    ]
                ]
            ],
            [
                'name' => '具体位置 Water pump',
                'code' => 'exactly_7',
                'options' => [
                    [
                        'name' => 'Water pump',
                        'content' => 1
                    ]
                ]
            ],
            [
                'name' => '具体位置 Spark plug',
                'code' => 'exactly_8',
                'description' => '具体位置 Spark plug类型',
                'options' => [
                    [
                        'name' => 'Ignition coil',
                        'content' => 1
                    ]
                ]
            ],
            [
                'name' => '具体位置 Oil suction pipe',
                'code' => 'exactly_9',
                'options' => [
                    [
                        'name' => 'Oil pump',
                        'content' => 1
                    ],
                    [
                        'name' => 'Oil cooler',
                        'content' => 2
                    ],
                    [
                        'name' => 'Oil filter',
                        'content' => 3
                    ],
                    [
                        'name' => 'Oil filter mounting hole surface',
                        'content' => 4
                    ],
                    [
                        'name' => 'Oil suction pipe',
                        'content' => 5
                    ],
                    [
                        'name' => 'Oil deflector',
                        'content' => 6
                    ],
                    [
                        'name' => 'Timing case',
                        'content' => 7
                    ],
                    [
                        'name' => 'Oil pump sealing',
                        'content' => 8
                    ]
                ]
            ],
            [
                'name' => '具体位置 Fuel injector',
                'code' => 'exactly_10',
                'options' => [
                    [
                        'name' => 'High pressure fuel pump',
                        'content' => 1
                    ],
                    [
                        'name' => 'High pressure fuel pipe',
                        'content' => 2
                    ],
                    [
                        'name' => 'Low pressure fuel pipe',
                        'content' => 3
                    ],
                    [
                        'name' => 'High pressure fuel pump soundproofing',
                        'content' => 4
                    ],
                    [
                        'name' => 'High pressure fuel pump shielding',
                        'content' => 5
                    ],
                    [
                        'name' => 'Roller tappet',
                        'content' => 6
                    ]
                ]
            ],
            [
                'name' => '具体位置 Turbocharger',
                'code' => 'exactly_11',
                'options' => [
                    [
                        'name' => 'Turbocharger heat shielding',
                        'content' => 1
                    ],
                    [
                        'name' => 'Turbocharger intake water pipe',
                        'content' => 2
                    ],
                    [
                        'name' => 'Turbocharger return water pipe',
                        'content' => 3
                    ],
                    [
                        'name' => 'Turbocharger intake oil pipe',
                        'content' => 4
                    ],
                    [
                        'name' => 'Turbocharger return oil pipe',
                        'content' => 5
                    ],
                    [
                        'name' => 'Turbocharger housing',
                        'content' => 6
                    ],
                    [
                        'name' => 'Exhaust side noise shielding',
                        'content' => 7
                    ]
                ]
            ],
            [
                'name' => '具体位置 CS',
                'code' => 'exactly_12',
                'options' => [
                    [
                        'name' => 'Main journal 1#',
                        'content' => 1
                    ],
                    [
                        'name' => 'Main journal 2#',
                        'content' => 2
                    ],
                    [
                        'name' => 'Main journal 3#',
                        'content' => 3
                    ],
                    [
                        'name' => 'Main journal 4#',
                        'content' => 4
                    ],
                    [
                        'name' => 'Main journal 5#',
                        'content' => 5
                    ],
                    [
                        'name' => 'Con-rod journal 1#',
                        'content' => 6
                    ],
                    [
                        'name' => 'Con-rod journal 2#',
                        'content' => 7
                    ],
                    [
                        'name' => 'Con-rod journal 3#',
                        'content' => 8
                    ],
                    [
                        'name' => 'Con-rod journal 4#',
                        'content' => 9
                    ],
                    [
                        'name' => 'Woodruff key',
                        'content' => 10
                    ],
                    [
                        'name' => 'Thrust washer',
                        'content' => 11
                    ],
                    [
                        'name' => 'Dynamic balancer',
                        'content' => 12
                    ],
                    [
                        'name' => 'Chain wheel',
                        'content' => 13
                    ],
                    [
                        'name' => 'Dynamic balancer cover',
                        'content' => 14
                    ],
                    [
                        'name' => 'Fly wheel',
                        'content' => 15
                    ],
                    [
                        'name' => 'Vibration damper',
                        'content' => 16
                    ]
                ]
            ],
            [
                'name' => '具体位置 Timing chain',
                'code' => 'exactly_13',
                'options' => [
                    [
                        'name' => 'Timing chain slide',
                        'content' => 1
                    ],
                    [
                        'name' => 'Bearing bolt',
                        'content' => 2
                    ],
                    [
                        'name' => 'Chain tensioning rail',
                        'content' => 3
                    ],
                    [
                        'name' => 'Chain tensioner',
                        'content' => 4
                    ],
                    [
                        'name' => 'Chain tensioner bracket',
                        'content' => 5
                    ],
                    [
                        'name' => 'Closing cover',
                        'content' => 6
                    ],
                    [
                        'name' => 'Idler gear',
                        'content' => 7
                    ],
                    [
                        'name' => 'Idler gear bushing',
                        'content' => 8
                    ]
                ]
            ],
            [
                'name' => '具体位置 Roller cam follower',
                'code' => 'exactly_14',
                'options' => [
                    [
                        'name' => 'Valve spring cap lowe',
                        'content' => 1
                    ],
                    [
                        'name' => 'Stud bolt',
                        'content' => 2
                    ],
                    [
                        'name' => 'Water temperature sensor',
                        'content' => 3
                    ],
                    [
                        'name' => 'Cylinder head gasket',
                        'content' => 4
                    ],
                    [
                        'name' => 'Water channel',
                        'content' => 5
                    ],
                    [
                        'name' => 'Oil channel',
                        'content' => 6
                    ],
                    [
                        'name' => 'CH sealing cover',
                        'content' => 7
                    ],
                    [
                        'name' => 'Valve collar',
                        'content' => 8
                    ],
                    [
                        'name' => 'Valve clearance compensating element',
                        'content' => 9
                    ],
                    [
                        'name' => 'Valve spring cap',
                        'content' => 10
                    ],
                    [
                        'name' => 'Intake camshaft adjuster',
                        'content' => 11
                    ],
                    [
                        'name' => 'Valve stem seal',
                        'content' => 12
                    ],
                    [
                        'name' => 'Valve spring',
                        'content' => 13
                    ],
                    [
                        'name' => 'Exhaust valve',
                        'content' => 14
                    ],
                    [
                        'name' => 'Intake valve',
                        'content' => 15
                    ],
                    [
                        'name' => 'Lift solenoid',
                        'content' => 16
                    ],
                    [
                        'name' => 'Exhaust central valve',
                        'content' => 17
                    ],
                    [
                        'name' => 'Intake central valve',
                        'content' => 18
                    ],
                    [
                        'name' => 'Exhaust camshaft adjuster',
                        'content' => 19
                    ]
                ]
            ],
            [
                'name' => '具体位置 Camshaft',
                'code' => 'exactly_15',
                'options' => [
                    [
                        'name' => 'Intake camshaft journal 1#',
                        'content' => 1
                    ],
                    [
                        'name' => 'Intake camshaft journal 2#',
                        'content' => 2
                    ],
                    [
                        'name' => 'Intake camshaft journal 3#',
                        'content' => 3
                    ],
                    [
                        'name' => 'Intake camshaft journal 4#',
                        'content' => 4
                    ],
                    [
                        'name' => 'Intake camshaft journal 5#',
                        'content' => 5
                    ],
                    [
                        'name' => 'Intake camshaft journal 6#',
                        'content' => 6
                    ],
                    [
                        'name' => 'Exhaust camshaft journal 1#',
                        'content' => 7
                    ],
                    [
                        'name' => 'Exhaust camshaft journal 2#',
                        'content' => 8
                    ],
                    [
                        'name' => 'Exhaust camshaft journal 3#',
                        'content' => 9
                    ],
                    [
                        'name' => 'Exhaust camshaft journal 4#',
                        'content' => 10
                    ],
                    [
                        'name' => 'Exhaust camshaft journal 5#',
                        'content' => 11
                    ],
                    [
                        'name' => 'Exhaust camshaft journal 6#',
                        'content' => 12
                    ],
                    [
                        'name' => 'Stud bolt',
                        'content' => 13
                    ],
                    [
                        'name' => 'Water temperature sensor',
                        'content' => 14
                    ],
                    [
                        'name' => 'Cylinder head gasket',
                        'content' => 15
                    ],
                    [
                        'name' => 'Water channel',
                        'content' => 16
                    ],
                    [
                        'name' => 'Oil channel',
                        'content' => 17
                    ],
                    [
                        'name' => 'CH sealing cover',
                        'content' => 18
                    ],
                    [
                        'name' => 'Inside camshaft cover mounting hole',
                        'content' => 19
                    ],
                    [
                        'name' => 'Exhaust camshaft cover mounting hole',
                        'content' => 20
                    ]
                ]
            ],
            [
                'name' => '具体位置 CH',
                'code' => 'exactly_16',
                'options' => [
                    [
                        'name' => 'Intake camshaft bearing 1#',
                        'content' => 1
                    ],
                    [
                        'name' => 'Intake camshaft bearing 2#',
                        'content' => 2
                    ],
                    [
                        'name' => 'Intake camshaft bearing 3#',
                        'content' => 3
                    ],
                    [
                        'name' => 'Intake camshaft bearing 4#',
                        'content' => 4
                    ],
                    [
                        'name' => 'Intake camshaft bearing 5#',
                        'content' => 5
                    ],
                    [
                        'name' => 'Intake camshaft bearing 6#',
                        'content' => 6
                    ],
                    [
                        'name' => 'Exhaust camshaft bearing 1#',
                        'content' => 7
                    ],
                    [
                        'name' => 'Exhaust camshaft bearing 2#',
                        'content' => 8
                    ],
                    [
                        'name' => 'Exhaust camshaft bearing 3#',
                        'content' => 9
                    ],
                    [
                        'name' => 'Exhaust camshaft bearing 4#',
                        'content' => 10
                    ],
                    [
                        'name' => 'Exhaust camshaft bearing 5#',
                        'content' => 11
                    ],
                    [
                        'name' => 'Exhaust camshaft bearing 6#',
                        'content' => 12
                    ],
                    [
                        'name' => 'CH exhaust side water cover 1#',
                        'content' => 13
                    ],
                    [
                        'name' => 'CH exhaust side water cover 2#',
                        'content' => 14
                    ],
                    [
                        'name' => 'CH exhaust side water cover 3#',
                        'content' => 15
                    ],
                    [
                        'name' => 'Stud bolt',
                        'content' => 16
                    ],
                    [
                        'name' => 'Water temperature sensor',
                        'content' => 17
                    ],
                    [
                        'name' => 'Cylinder head gasket',
                        'content' => 18
                    ],
                    [
                        'name' => 'Water channel',
                        'content' => 19
                    ],
                    [
                        'name' => 'Oil channel',
                        'content' => 20
                    ],
                    [
                        'name' => 'CH sealing cover',
                        'content' => 21
                    ],
                    [
                        'name' => 'CH cover high pressure fuel pump mounting hole',
                        'content' => 22
                    ],
                    [
                        'name' => 'CH cover metal pipe',
                        'content' => 23
                    ],
                    [
                        'name' => 'CH oil channel',
                        'content' => 24
                    ],
                    [
                        'name' => 'Cylinder head bottom',
                        'content' => 25
                    ],
                    [
                        'name' => 'CH bottom surface',
                        'content' => 26
                    ],
                    [
                        'name' => 'Cylinder head surface',
                        'content' => 27
                    ]
                ]
            ],
            [
                'name' => '具体位置 Con-rod bearing',
                'code' => 'exactly_17',
                'options' => [
                    [
                        'name' => 'Con-rod bearing 1# upper side',
                        'content' => 1
                    ],
                    [
                        'name' => 'Con-rod bearing 2# upper side',
                        'content' => 2
                    ],
                    [
                        'name' => 'Con-rod bearing 3# upper side',
                        'content' => 3
                    ],
                    [
                        'name' => 'Con-rod bearing 4# upper side',
                        'content' => 4
                    ],
                    [
                        'name' => 'Con-rod bearing 1# lower side',
                        'content' => 5
                    ],
                    [
                        'name' => 'Con-rod bearing 2# lower side',
                        'content' => 6
                    ],
                    [
                        'name' => 'Con-rod bearing 3# lower side',
                        'content' => 7
                    ],
                    [
                        'name' => 'Con-rod bearing 4# lower side',
                        'content' => 8
                    ]
                ]
            ],
            [
                'name' => '具体位置 Main bearing',
                'code' => 'exactly_18',
                'options' => [
                    [
                        'name' => 'Main bearing 1# upper side',
                        'content' => 1
                    ],
                    [
                        'name' => 'Main bearing 2# upper side',
                        'content' => 2
                    ],
                    [
                        'name' => 'Main bearing 3# upper side',
                        'content' => 3
                    ],
                    [
                        'name' => 'Main bearing 4# upper side',
                        'content' => 4
                    ],
                    [
                        'name' => 'Main bearing 5# upper side',
                        'content' => 5
                    ],
                    [
                        'name' => 'Main bearing 1# lower side',
                        'content' => 6
                    ],
                    [
                        'name' => 'Main bearing 2# lower side',
                        'content' => 7
                    ],
                    [
                        'name' => 'Main bearing 3# lower side',
                        'content' => 8
                    ],
                    [
                        'name' => 'Main bearing 4# lower side',
                        'content' => 9
                    ],
                    [
                        'name' => 'Main bearing 5# lower side',
                        'content' => 10
                    ]
                ]
            ],
            [
                'name' => '具体位置 Piston pin',
                'code' => 'exactly_19',
                'options' => [
                    [
                        'name' => '1# Piston pin front side',
                        'content' => 1
                    ],
                    [
                        'name' => '2# Piston pin front side',
                        'content' => 2
                    ],
                    [
                        'name' => '3# Piston pin front side',
                        'content' => 3
                    ],
                    [
                        'name' => '4# Piston pin front side',
                        'content' => 4
                    ],
                    [
                        'name' => '1# Piston pin rear side',
                        'content' => 5
                    ],
                    [
                        'name' => '2# Piston pin rear side',
                        'content' => 6
                    ],
                    [
                        'name' => '3# Piston pin rear side',
                        'content' => 7
                    ],
                    [
                        'name' => '4# Piston pin rear side',
                        'content' => 8
                    ]
                ]
            ],
            [
                'name' => '具体位置 Piston skirt',
                'code' => 'exactly_20',
                'options' => [
                    [
                        'name' => '1# Piston skirt intake side',
                        'content' => 1
                    ],
                    [
                        'name' => '2# Piston skirt intake side',
                        'content' => 2
                    ],
                    [
                        'name' => '3# Piston skirt intake side',
                        'content' => 3
                    ],
                    [
                        'name' => '4# Piston skirt intake side',
                        'content' => 4
                    ],
                    [
                        'name' => '1# Piston skirt exhaust side',
                        'content' => 5
                    ],
                    [
                        'name' => '2# Piston skirt exhaust side',
                        'content' => 6
                    ],
                    [
                        'name' => '3# Piston skirt exhaust side',
                        'content' => 7
                    ],
                    [
                        'name' => '4# Piston skirt exhaust side',
                        'content' => 8
                    ],
                    [
                        'name' => 'Piston ring 1#',
                        'content' => 9
                    ],
                    [
                        'name' => 'Piston ring 2#',
                        'content' => 10
                    ],
                    [
                        'name' => 'Piston oil ring',
                        'content' => 11
                    ]
                ]
            ],
            [
                'name' => '具体位置 Con-rod',
                'code' => 'exactly_21',
                'options' => [
                    [
                        'name' => 'Con-rod small eye bush',
                        'content' => 1
                    ],
                    [
                        'name' => 'Con-rod screw',
                        'content' => 2
                    ],
                    [
                        'name' => 'Con-rod',
                        'content' => 3
                    ],
                    [
                        'name' => 'Con-rod bearing',
                        'content' => 4
                    ],
                    [
                        'name' => 'Con-rod bearing 1# upper side',
                        'content' => 5
                    ],
                    [
                        'name' => 'Con-rod bearing 2# upper side',
                        'content' => 6
                    ],
                    [
                        'name' => 'Con-rod bearing 3# upper side',
                        'content' => 7
                    ],
                    [
                        'name' => 'Con-rod bearing 4# upper side',
                        'content' => 8
                    ],
                    [
                        'name' => 'Con-rod bearing 1# lower side',
                        'content' => 9
                    ],
                    [
                        'name' => 'Con-rod bearing 2# lower side',
                        'content' => 10
                    ],
                    [
                        'name' => 'Con-rod bearing 3# lower side',
                        'content' => 11
                    ],
                    [
                        'name' => 'Con-rod bearing 4# lower side',
                        'content' => 12
                    ]
                ]
            ],
            [
                'name' => '具体位置 CB',
                'code' => 'exactly_22',
                'options' => [
                    [
                        'name' => 'CB oil channel',
                        'content' => 1
                    ],
                    [
                        'name' => 'Water channel',
                        'content' => 2
                    ],
                    [
                        'name' => 'Oil channel',
                        'content' => 3
                    ],
                    [
                        'name' => 'Main bearing seat',
                        'content' => 4
                    ],
                    [
                        'name' => 'Oil pan screw  mounting hole thread',
                        'content' => 5
                    ],
                    [
                        'name' => 'CH screw  mounting hole thread',
                        'content' => 6
                    ],
                    [
                        'name' => 'Engine mount RH screw  mounting hole',
                        'content' => 7
                    ],
                    [
                        'name' => 'Engine mount LH screw  mounting hole',
                        'content' => 8
                    ],
                    [
                        'name' => 'End cover of crankshaft sealing',
                        'content' => 9
                    ],
                    [
                        'name' => 'CB water channel',
                        'content' => 10
                    ],
                    [
                        'name' => 'Cylinder bore 1# intake side',
                        'content' => 11
                    ],
                    [
                        'name' => 'Cylinder bore 2# intake side',
                        'content' => 12
                    ],
                    [
                        'name' => 'Cylinder bore 3# intake side',
                        'content' => 13
                    ],
                    [
                        'name' => 'Cylinder bore 4# intake side',
                        'content' => 14
                    ],
                    [
                        'name' => 'Cylinder bore 1# exhaust side',
                        'content' => 15
                    ],
                    [
                        'name' => 'Cylinder bore 2# exhaust side',
                        'content' => 16
                    ],
                    [
                        'name' => 'Cylinder bore 3# exhaust side',
                        'content' => 17
                    ],
                    [
                        'name' => 'Cylinder bore 4# exhaust side',
                        'content' => 18
                    ]
                ]
            ],
            [
                'name' => '整车服务班次',
                'code' => 'service_shift',
                'options' => [
                    [
                        'name' => 'AD',
                        'content' => 1
                    ],
                    [
                        'name' => 'AN',
                        'content' => 2
                    ],
                    [
                        'name' => 'BD',
                        'content' => 3
                    ],
                    [
                        'name' => 'BN',
                        'content' => 4
                    ]
                ]
            ],
            [
                'name' => '整车服务厂区',
                'code' => 'service_factory',
                'options' => [
                    [
                        'name' => 'FBAC',
                        'content' => 1
                    ],
                    [
                        'name' => 'MFA',
                        'content' => 2
                    ],
                    [
                        'name' => 'MRA1',
                        'content' => 3
                    ],
                    [
                        'name' => 'MRA2',
                        'content' => 4
                    ],
                    [
                        'name' => 'Shunyi',
                        'content' => 5
                    ]
                ]
            ],
            [
                'name' => 'E&B Type发动机/电池型号',
                'code' => 'eb_type',
                'options' => [
                    [
                        'name' => 'M260',
                        'content' => 1
                    ],
                    [
                        'name' => 'M260A',
                        'content' => 2
                    ],
                    [
                        'name' => 'EB421',
                        'content' => 3
                    ],
                    [
                        'name' => 'EB422',
                        'content' => 4
                    ],
                ]
            ],
            [
                'name' => '问题状态',
                'code' => 'issue_status',
                'options' => [
                    [
                        'name' => 'Verify',
                        'content' => 1
                    ],
                    [
                        'name' => 'Ongoing',
                        'content' => 2
                    ],
                    [
                        'name' => 'Closed',
                        'content' => 3
                    ],
                    [
                        'name' => 'Overdue',
                        'content' => 4
                    ],
                ]
            ],
            [
                'name' => '扭矩监控状态',
                'code' => 'inline_item_type_1',
                'options' => [
                    [
                        'name' => '超上差',
                        'content' => 1
                    ],
                    [
                        'name' => '超下差',
                        'content' => 2
                    ],
                    [
                        'name' => '超下预警线',
                        'content' => 3
                    ],
                    [
                        'name' => '超上预警线',
                        'content' => 4
                    ]
                ]
            ],
            [
                'name' => '尺寸测量状态',
                'code' => 'inline_item_type_2',
                'options' => [
                    [
                        'name' => '超上差',
                        'content' => 1
                    ],
                    [
                        'name' => '超下差',
                        'content' => 2
                    ]
                ]
            ],
            [
                'name' => '外观检测状态',
                'code' => 'inline_item_type_3',
                'options' => [
                    [
                        'name' => '损坏',
                        'content' => 1
                    ],
                    [
                        'name' => '缺失',
                        'content' => 2
                    ],
                    [
                        'name' => '多余材料',
                        'content' => 3
                    ],
                    [
                        'name' => '错装',
                        'content' => 4
                    ],
                    [
                        'name' => '脏污',
                        'content' => 5
                    ],
                    [
                        'name' => '其他',
                        'content' => 6
                    ]
                ]
            ],
            [
                'name' => '过程监控状态',
                'code' => 'inline_item_type_4',
                'options' => [
                    [
                        'name' => '异常',
                        'content' => 1
                    ]
                ]
            ],
            [
                'name' => '墨水测试状态',
                'code' => 'inline_item_type_5',
                'options' => [
                    [
                        'name' => '不合格',
                        'content' => 1
                    ]
                ]
            ],
            [
                'name' => '撕胶测试状态',
                'code' => 'inline_item_type_6',
                'options' => [
                    [
                        'name' => '不合格',
                        'content' => 1
                    ]
                ]
            ],
            [
                'name' => '触发考核状态',
                'code' => 'inline_item_type_7',
                'options' => [
                    [
                        'name' => '异常',
                        'content' => 1
                    ]
                ]
            ],
            [
                'name' => '试装支持状态',
                'code' => 'inline_item_type_8',
                'options' => [
                    [
                        'name' => '异常',
                        'content' => 1
                    ]
                ]
            ],
            [
                'name' => '项目支持状态',
                'code' => 'inline_item_type_9',
                'options' => [
                    [
                        'name' => '异常',
                        'content' => 1
                    ]
                ]
            ],
            [
                'name' => '处理时间状态',
                'code' => 'due_date_status',
                'options' => [
                    [
                        'name' => '富裕',
                        'content' => 1
                    ],
                    [
                        'name' => '临近',
                        'content' => 2
                    ],
                    [
                        'name' => '超出',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '探测区域',
                'code' => 'detect_area',
                'options' => [
                    [
                        'name' => 'Assembly Station',
                        'content' => 1
                    ],
                    [
                        'name' => 'Q-Gate(Inline)',
                        'content' => 2
                    ],
                    [
                        'name' => 'Cold Test',
                        'content' => 3
                    ],
                    [
                        'name' => 'Q-Gate(FA)',
                        'content' => 4
                    ],
                    [
                        'name' => 'Vehicle Plant',
                        'content' => 5
                    ]
                ]
            ],
            [
                'name' => '问题类型',
                'code' => 'issue_type',
                'options' => [
                    [
                        'name' => 'Function 功能故障',
                        'content' => 1
                    ],
                    [
                        'name' => 'Missing/Wrong assembly 错/漏装',
                        'content' => 2
                    ],
                    [
                        'name' => 'Damage 损伤',
                        'content' => 3
                    ],
                    [
                        'name' => 'Cleanliness 清洁度',
                        'content' => 4
                    ],
                    [
                        'name' => 'Deformation 变形',
                        'content' => 5
                    ]
                ]
            ],
            [
                'name' => '滞留状态',
                'code' => 'block_status',
                'options' => [
                    [
                        'name' => '待返修',
                        'content' => 1
                    ],
                    [
                        'name' => '待整车检测',
                        'content' => 2
                    ],
                    [
                        'name' => '已放行',
                        'content' => 3
                    ]
                ]
            ],
            [
                'name' => '整车状态',
                'code' => 'service_status',
                'options' => [
                    [
                        'name' => '待确认',
                        'content' => 1
                    ],
                    [
                        'name' => '已关闭',
                        'content' => 2
                    ]
                ]
            ],
            [
                'name' => '整车检测内容',
                'code' => 'block_content',
                'options' => [
                    [
                        'name' => 'IS电检',
                        'content' => 1
                    ],
                    [
                        'name' => '试漏',
                        'content' => 2
                    ],
                    [
                        'name' => '转毂测试',
                        'content' => 3
                    ],
                    [
                        'name' => '激光标签',
                        'content' => 4
                    ],
                    [
                        'name' => 'WSA',
                        'content' => 5
                    ]
                ]
            ],
            [
                'name' => '根本原因类型',
                'code' => 'root_cause_type',
                'options' => [
                    [
                        'name' => 'Part/零件',
                        'content' => 1
                    ],
                    [
                        'name' => 'Assembly/装配',
                        'content' => 2
                    ],
                    [
                        'name' => 'Quality/质量',
                        'content' => 3
                    ],
                    [
                        'name' => 'Logistic/物流',
                        'content' => 4
                    ],
                    [
                        'name' => 'Machining/机加工',
                        'content' => 5
                    ],
                    [
                        'name' => 'DIP',
                        'content' => 6
                    ],
                    [
                        'name' => 'Vehicle Plant/整车',
                        'content' => 7
                    ],
                    [
                        'name' => 'NC',
                        'content' => 8
                    ]
                ]
            ],
            [
                'name' => '问题发现点',
                'code' => 'sensor_point',
                'options' => [
                    [
                        'name' => 'Onsite check  上线前检查',
                        'content' => 1
                    ],
                    [
                        'name' => 'EDU',
                        'content' => 2
                    ],
                    [
                        'name' => 'CDU',
                        'content' => 3
                    ],
                    [
                        'name' => 'CH 底盘',
                        'content' => 4
                    ],
                    [
                        'name' => '底盘电检',
                        'content' => 5
                    ],
                    [
                        'name' => '转鼓',
                        'content' => 6
                    ],
                    [
                        'name' => 'WSA',
                        'content' => 7
                    ],
                    [
                        'name' => '库房',
                        'content' => 8
                    ],
                    [
                        'name' => 'VOCA',
                        'content' => 9
                    ],
                    [
                        'name' => 'GFP',
                        'content' => 10
                    ],
                    [
                        'name' => 'PAF',
                        'content' => 11
                    ]
                ]
            ],
            [
                'name' => '8D问题状态',
                'code' => 'eight_disciplines',
                'options' => [
                    [
                        'name' => '确认',
                        'content' => 1
                    ],
                    [
                        'name' => '进行中',
                        'content' => 2
                    ],
                    [
                        'name' => '逾期',
                        'content' => 3
                    ],
                    [
                        'name' => '关闭',
                        'content' => 4
                    ]
                ]
            ],
            [
                'name' => '系统参数类型',
                'code' => 'system_config_type',
                'options' => [
                    [
                        'name' => '单行文本',
                        'content' => 1,
                    ],
                    [
                        'name' => 'Switch开关',
                        'content' => 2
                    ],
                    [
                        'name' => '多行文本',
                        'content' => 3
                    ],
                    [
                        'name' => '副文本',
                        'content' => 4
                    ],
                    [
                        'name' => '单一图片',
                        'content' => 5
                    ],
                    [
                        'name' => '多图',
                        'content' => 6
                    ],
                    [
                        'name' => '数字',
                        'content' => 7
                    ]
                ]
            ],
            [
                'name' => '整车状态',
                'code' => 'status',
                'options' => [
                    [
                        'name' => '在线考核中',
                        'content' => 1,
                    ],
                    [
                        'name' => '产品考核中',
                        'content' => 2
                    ],
                    [
                        'name' => '动态考核中',
                        'content' => 3
                    ],
                    [
                        'name' => '整车滞留中',
                        'content' => 4
                    ],
                    [
                        'name' => '车辆已放行',
                        'content' => 5
                    ]
                ]
            ],
            [
                'name' => '部门属性',
                'code' => 'department_role',
                'options' => [
                    [
                        'name' => '不设置',
                        'content' => 0,
                    ],
                    [
                        'name' => '管理组',
                        'content' => 1,
                    ],
                    [
                        'name' => '在线考核',
                        'content' => 2,
                    ],
                    [
                        'name' => '产品考核',
                        'content' => 3,
                    ],
                    [
                        'name' => '整车服务',
                        'content' => 4,
                    ]
                ]
            ],
            [
                'name' => '指导书类型',
                'code' => 'document_type',
                'options' => [
                    [
                        'name' => '拆检指导书',
                        'content' => 1,
                    ],
                    [
                        'name' => '装配指导书',
                        'content' => 2,
                    ],
                    [
                        'name' => '零件清单',
                        'content' => 3,
                    ],
                    [
                        'name' => '扭矩清单',
                        'content' => 4,
                    ]
                ]
            ],
            [
                'name' => '应用意图',
                'code' => 'purpose',
                'options' => [
                    [
                        'name' => 'SOP',
                        'content' => 1,
                    ],
                    [
                        'name' => 'Project',
                        'content' => 2,
                    ],
                    [
                        'name' => 'Tryout',
                        'content' => 3,
                    ],
                    [
                        'name' => 'Analysis',
                        'content' => 4,
                    ]
                ]
            ]
        ];
    }
}
