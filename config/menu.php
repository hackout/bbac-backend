<?php

return [
    [
        'name' => '控制台',
        'intro' => 'Dashboard',
        'path' => 'dashboard',
        'icon' => 'bbac-icon-car',
        'show' => true
    ],
    [
        'name' => '个人中心',
        'path' => 'profile.index',
        'children' => [
            [
                'name' => '个人资料',
                'path' => 'profile.index',
            ],
            [
                'name' => '账号管理',
                'path' => 'profile.account',
            ],
            [
                'name' => '密码安全',
                'path' => 'profile.password',
            ],
            [
                'name' => '登录日志',
                'path' => 'profile.log',
            ],
        ]
    ],
    [
        'name' => '员工信息',
        'intro' => 'Employee Profile',
        'path' => 'department.index',
        'icon' => 'bbac-icon-user',
        'show' => true,
        'children' => [
            [
                'name' => '组织机构维护',
                'path' => 'department.index',
                'show' => true,
            ],
            [
                'name' => '角色维护',
                'path' => 'role.index',
                'show' => true,
            ],
            [
                'name' => '员工维护',
                'path' => 'user.index',
                'show' => true,
            ],
            [
                'name' => '安全培训',
                'path' => 'training.safe',
                'show' => true,
            ],
            [
                'name' => '技能培训',
                'path' => 'training.skill',
                'show' => true,
            ],
            [
                'name' => '综合培训',
                'path' => 'training.multiple',
                'show' => true,
            ],
            [
                'name' => '生日祝福',
                'path' => 'user.birthday',
                'show' => true,
            ]
        ]
    ],
    [
        'name' => '通知消息',
        'intro' => 'Notification',
        'path' => 'notice.index',
        'icon' => 'el-icon-chat-line-square',
        'show' => true,
        'children' => [
            [
                'name' => '消息通知',
                'path' => 'notice.index',
                'show' => true,
            ]
        ]
    ],
    [
        'name' => '工艺文件',
        'intro' => 'Process Files',
        'path' => 'file.index',
        'icon' => 'el-icon-files',
        'show' => true,
        'children' => [
            [
                'name' => '知识库管理',
                'path' => 'file.index',
                'show' => true,
            ]
        ]
    ],
    [
        'name' => '基础数据维护',
        'intro' => 'Basic Data',
        'path' => 'assembly.index',
        'icon' => 'el-icon-set-up',
        'show' => true,
        'children' => [
            [
                'name' => '基础信息维护',
                'path' => 'assembly.index',
                'show' => true,
            ],
            [
                'name' => '拆检指导书',
                'path' => 'document.overhaul',
                'show' => true,
            ],
            [
                'name' => '装配指导书',
                'path' => 'document.assembling',
                'show' => true,
            ],
            [
                'name' => '零件清单',
                'path' => 'part.index',
                'show' => true,
            ],
            [
                'name' => '扭矩清单',
                'path' => 'document.torque',
                'show' => true,
            ],
            [
                'name' => '发动机清单',
                'path' => 'product.index',
                'show' => true,
            ]
        ]
    ],
    [
        'name' => '考核定义',
        'intro' => 'Examine Settings',
        'path' => 'examine.index',
        'icon' => 'el-icon-guide',
        'show' => true,
        'children' => [
            [
                'name' => '考核列表',
                'path' => 'examine.index',
                'show' => true,
            ],
            [
                'name' => '在线考核',
                'path' => 'commit_inline.index',
                'show' => true,
            ],
            [
                'name' => '产品考核',
                'path' => 'commit_product.index',
                'show' => true,
            ],
            [
                'name' => '整车服务',
                'path' => 'commit_vehicle.index',
                'show' => true,
            ]
        ]
    ],
    [
        'name' => '任务中心',
        'intro' => 'Task Center',
        'path' => 'task.index',
        'icon' => 'el-icon-collection',
        'show' => true,
        'children' => [
            [
                'name' => '考核单中心',
                'path' => 'task.index',
                'show' => true,
            ],
            [
                'name' => '任务配置',
                'path' => 'task_cron.index',
                'show' => true,
            ]
        ]
    ],
    [
        'name' => '任务分配',
        'intro' => 'Task Assignment',
        'path' => 'work.index',
        'icon' => 'el-icon-memo',
        'show' => true,
        'children' => [
            [
                'name' => '任务分配',
                'path' => 'work.index',
                'show' => true,
            ],
        ]
    ],
    [
        'name' => '产品考核',
        'intro' => 'Product Audit',
        'path' => 'stuff.index',
        'icon' => 'el-icon-suitcase',
        'show' => true,
        'children' => [
            [
                'name' => '产品考核',
                'path' => 'stuff.index',
                'show' => true,
            ],
            [
                'name' => '发动机装配',
                'path' => 'stuff.engine',
                'show' => true,
            ],
            [
                'name' => '动态考核',
                'path' => 'stuff.dynamic',
                'show' => true,
            ],
            [
                'name' => '问题追踪',
                'path' => 'stuff.issue',
                'show' => true,
            ]
        ]
    ],
    [
        'name' => '在线考核',
        'intro' => 'Inline Audit',
        'path' => 'issue.inline',
        'icon' => 'el-icon-watch',
        'show' => true,
        'children' => [
            [
                'name' => '问题跟踪',
                'path' => 'issue.inline',
                'show' => true,
            ],
            [
                'name' => '扭矩数据库',
                'path' => 'torque.index',
                'show' => true,
            ],
            [
                'name' => 'SPC',
                'path' => 'torque_item.index',
                'show' => true,
            ],
            [
                'name' => '排产计划',
                'path' => 'plan.index',
                'show' => true,
            ]
        ]
    ],
    [
        'name' => '整车服务',
        'intro' => 'i-Service',
        'path' => 'vehicle.index',
        'icon' => 'el-icon-stopwatch',
        'show' => true,
        'children' => [
            [
                'name' => '待处理问题',
                'path' => 'vehicle.index',
                'show' => true,
            ],
            [
                'name' => '已处理问题',
                'path' => 'vehicle.finish',
                'show' => true,
            ],
            [
                'name' => '动态考核',
                'path' => 'vehicle.task',
                'show' => true,
            ],
            [
                'name' => '每日发运量',
                'path' => 'vehicle.outbound',
                'show' => true,
            ],
            [
                'name' => 'PPM Target',
                'path' => 'vehicle.target',
                'show' => true,
            ]
        ]
    ],
    [
        'name' => '数据报表',
        'intro' => 'Data Report',
        'path' => 'report.inline',
        'icon' => 'el-icon-data-line',
        'show' => true,
        'children' => [
            [
                'name' => '在线考核',
                'path' => 'report.inline',
                'show' => true,
            ],
            [
                'name' => '产品考核',
                'path' => 'report.product',
                'show' => true,
            ],
            [
                'name' => '整车服务',
                'path' => 'report.vehicle',
                'show' => true,
            ]
        ]
    ],
    [
        'name' => '系统设置',
        'intro' => 'System Settings',
        'path' => 'system_config.index',
        'icon' => 'el-icon-setting',
        'show' => true,
        'children' => [
            [
                'name' => '基础参数设置',
                'path' => 'system_config.index',
                'show' => true,
            ],
            [
                'name' => '语言包管理',
                'path' => 'locale_package.index',
                'show' => true,
            ],
            [
                'name' => '数据字典管理',
                'path' => 'dict.index',
                'show' => true,
            ],
            [
                'name' => '后台访问日志',
                'path' => 'user_log.index',
                'show' => true,
            ],
            [
                'name' => '系统缓存',
                'path' => 'system_config.cache',
                'show' => true,
            ]
        ]
    ],
];