<?php

return [
    [
        'name' => '角色管理',
        'code' => 'role.index',
        'children' => [
            [
                'name' => '查询记录',
                'code' => 'role.list'
            ],
            [
                'name' => '创建信息',
                'code' => 'role.create'
            ],
            [
                'name' => '批量删除',
                'code' => 'role.batch_delete'
            ],
            [
                'name' => '更新信息',
                'code' => 'role.update'
            ],
            [
                'name' => '设置有效性',
                'code' => 'role.valid'
            ]
        ]
    ],
    [
        'name' => '组织机构管理',
        'code' => 'department.index',
        'children' => [
            [
                'name' => '查询记录',
                'code' => 'department.list'
            ],
            [
                'name' => '下载模板',
                'code' => 'department.template'
            ],
            [
                'name' => '创建信息',
                'code' => 'department.create'
            ],
            [
                'name' => '导入信息',
                'code' => 'department.import'
            ],
            [
                'name' => '批量删除',
                'code' => 'department.batch_delete'
            ],
            [
                'name' => '更新信息',
                'code' => 'department.update'
            ]
        ]
    ],
    [
        'name' => '字典管理',
        'code' => 'dict.index',
        'children' => [
            [
                'name' => '查询记录',
                'code' => 'dict.list'
            ],
            [
                'name' => '创建信息',
                'code' => 'dict.create'
            ],
            [
                'name' => '导出信息',
                'code' => 'dict.export'
            ],
            [
                'name' => '更新信息',
                'code' => 'dict.update'
            ],
            [
                'name' => '字典项记录',
                'code' => 'dict_item.list'
            ],
            [
                'name' => '创建字典项',
                'code' => 'dict_item.create'
            ],
            [
                'name' => '删除字典项',
                'code' => 'dict_item.batch_delete'
            ],
            [
                'name' => '更新字典项',
                'code' => 'dict_item.update'
            ]
        ]
    ],
    [
        'name' => '系统设置',
        'code' => 'system.index'
    ],
    [
        'name' => '员工管理',
        'code' => 'user.index',
        'children' => [
            [
                'name' => '查询列表',
                'code' => 'user.list'
            ],
            [
                'name' => '创建信息',
                'code' => 'user.create'
            ],
            [
                'name' => '批量删除',
                'code' => 'user.batch_delete'
            ],
            [
                'name' => '导出信息',
                'code' => 'user.export'
            ],
            [
                'name' => '导入信息',
                'code' => 'user.import'
            ],
            [
                'name' => '下载模板',
                'code' => 'user.template'
            ],
            [
                'name' => '获取详情',
                'code' => 'user.detail'
            ],
            [
                'name' => '更新账户信息',
                'code' => 'user.update'
            ],
            [
                'name' => '修改详细资料',
                'code' => 'user.update_detail'
            ],
            [
                'name' => '修改状态',
                'code' => 'user.patch'
            ],
            [
                'name' => '部门查询员工',
                'code' => 'user.department'
            ],
            [
                'name' => '生日祝福',
                'code' => 'user.birthday'
            ],
            [
                'name' => '生日列表',
                'code' => 'user.birthday_list'
            ],
            [
                'name' => '设置祝福卡',
                'code' => 'user.birthday_update'
            ]
        ]
    ],
    [
        'name' => '生日祝福卡模板',
        'code' => 'birthday_card.index',
        'children' => [
            [
                'name' => '查询列表',
                'code' => 'birthday_card.list'
            ],
            [
                'name' => '创建信息',
                'code' => 'birthday_card.create'
            ],
            [
                'name' => '编辑信息',
                'code' => 'birthday_card.update'
            ],
            [
                'name' => '删除信息',
                'code' => 'birthday_card.delete'
            ],
        ]
    ],
    [
        'name' => '培训管理',
        'code' => 'training.index',
        'children' => [
            [
                'name' => '安全培训',
                'code' => 'training.safe'
            ],
            [
                'name' => '技能培训',
                'code' => 'training.skill'
            ],
            [
                'name' => '综合培训',
                'code' => 'training.multiple'
            ],
            [
                'name' => '查询列表',
                'code' => 'training.list'
            ],
            [
                'name' => '上传附件',
                'code' => 'training.upload'
            ],
            [
                'name' => '删除附件',
                'code' => 'training.file_delete'
            ],
            [
                'name' => '批量删除',
                'code' => 'training.batch_delete'
            ],
            [
                'name' => '导出信息',
                'code' => 'training.export'
            ],
            [
                'name' => '导入信息',
                'code' => 'training.import'
            ],
            [
                'name' => '下载模板',
                'code' => 'training.template'
            ],
            [
                'name' => '更新信息',
                'code' => 'training.update'
            ],
            [
                'name' => '修改状态',
                'code' => 'training.patch'
            ]
        ]
    ],
    [
        'name' => '知识库管理',
        'code' => 'file.index',
        'children' => [
            [
                'name' => '获取列表',
                'code' => 'file.list'
            ],
            [
                'name' => '添加文件夹',
                'code' => 'file.create'
            ],
            [
                'name' => '上传文件',
                'code' => 'file.upload'
            ],
            [
                'name' => '批量删除',
                'code' => 'file.batch_delete'
            ],
            [
                'name' => '批量移动',
                'code' => 'file.batch_move'
            ],
            [
                'name' => '更新文件(夹)',
                'code' => 'file.update'
            ],
            [
                'name' => '移动文件(夹)',
                'code' => 'file.move'
            ],
            [
                'name' => '删除文件(夹)',
                'code' => 'file.delete'
            ],
            [
                'name' => '下载文件(夹)',
                'code' => 'file.download'
            ],
            [
                'name' => '预览文件',
                'code' => 'file.view'
            ]
        ]
    ],
    [
        'name' => '语言包管理',
        'code' => 'locale_package.index',
        'children' => [
            [
                'name' => '获取列表',
                'code' => 'locale_package.list'
            ],
            [
                'name' => '创建数据',
                'code' => 'locale_package.create'
            ],
            [
                'name' => '导出数据',
                'code' => 'locale_package.export'
            ],
            [
                'name' => '导入数据',
                'code' => 'locale_package.import'
            ],
            [
                'name' => '下载模板',
                'code' => 'locale_package.template'
            ],
            [
                'name' => '更新数据',
                'code' => 'locale_package.update'
            ]
        ]
    ],
    [
        'name' => '总成信息管理',
        'code' => 'assembly.index',
        'children' => [
            [
                'name' => '获取列表',
                'code' => 'assembly.list'
            ],
            [
                'name' => '获取选项',
                'code' => 'assembly.option'
            ],
            [
                'name' => '创建数据',
                'code' => 'assembly.create'
            ],
            [
                'name' => '导出数据',
                'code' => 'assembly.export'
            ],
            [
                'name' => '导入数据',
                'code' => 'assembly.import'
            ],
            [
                'name' => '下载模板',
                'code' => 'assembly.template'
            ],
            [
                'name' => '更新数据',
                'code' => 'assembly.update'
            ],
            [
                'name' => '删除数据',
                'code' => 'assembly.delete'
            ]
        ]
    ],
    [
        'name' => '扭矩数据库',
        'code' => 'torque.index',
        'children' => [
            [
                'name' => '获取列表',
                'code' => 'torque.list'
            ],
            [
                'name' => '导入数据',
                'code' => 'torque.import'
            ],
            [
                'name' => '下载模板',
                'code' => 'torque.template'
            ],
            [
                'name' => '更新数据',
                'code' => 'torque.update'
            ],
            [
                'name' => '变更记录',
                'code' => 'torque_change_record.list'
            ]
        ]
    ],
    [
        'name' => 'SPC',
        'code' => 'torque_item.index',
        'children' => [
            [
                'name' => '获取列表',
                'code' => 'torque_item.list'
            ],
            [
                'name' => '导出数据',
                'code' => 'torque_item.export'
            ],
            [
                'name' => '更新数据',
                'code' => 'torque_item.update'
            ]
        ]
    ],
    [
        'name' => '问题追踪',
        'code' => 'issue.index',
        'children' => [
            [
                'name' => '在线考核',
                'code' => 'issue.inline'
            ],
            [
                'name' => '产品考核',
                'code' => 'issue.product'
            ],
            [
                'name' => '整车服务-待处理',
                'code' => 'issue.service'
            ],
            [
                'name' => '整车服务-已处理',
                'code' => 'issue.finish'
            ],
            [
                'name' => '查看详情',
                'code' => 'issue.detail'
            ],
            [
                'name' => '获取列表',
                'code' => 'issue.list'
            ],
            [
                'name' => '导出数据',
                'code' => 'issue.export'
            ],
            [
                'name' => '更新数据',
                'code' => 'issue.update'
            ]
        ]
    ],
    [
        'name' => '考核模板',
        'code' => 'examine.index',
        'children' => [
            [
                'name' => '已审核考核',
                'code' => 'examine.index'
            ],
            [
                'name' => '获取列表',
                'code' => 'examine.list'
            ],
            [
                'name' => '选项列表',
                'code' => 'examine.option'
            ],
            [
                'name' => '导出数据',
                'code' => 'examine.export'
            ],
            [
                'name' => '删除数据',
                'code' => 'examine.delete'
            ]
        ]
    ],
    [
        'name' => '考核定义',
        'code' => 'commit.index',
        'children' => [
            [
                'name' => '在线考核',
                'code' => 'commit.inline'
            ],
            [
                'name' => '产品考核',
                'code' => 'commit.product'
            ],
            [
                'name' => '整车服务',
                'code' => 'commit.service'
            ],
            [
                'name' => '添加数据',
                'code' => 'commit.create'
            ],
            [
                'name' => '导入数据',
                'code' => 'commit.import'
            ],
            [
                'name' => '下载模板',
                'code' => 'commit.template'
            ],
            [
                'name' => '获取列表',
                'code' => 'commit.list'
            ],
            [
                'name' => '导出数据',
                'code' => 'commit.export'
            ],
            [
                'name' => '获取变更项',
                'code' => 'commit.changed'
            ],
            [
                'name' => '更新数据',
                'code' => 'commit.update'
            ],
            [
                'name' => '查看详情',
                'code' => 'commit.detail'
            ],
            [
                'name' => '提交审核',
                'code' => 'commit_approve.create'
            ],
            [
                'name' => '删除数据',
                'code' => 'commit.delete'
            ]
        ]
    ],
    [
        'name' => '考核定义-考核项',
        'code' => 'commit_item.index',
        'children' => [
            [
                'name' => '获取列表',
                'code' => 'commit_item.list'
            ],
            [
                'name' => '添加数据',
                'code' => 'commit_item.create'
            ],
            [
                'name' => '更新数据',
                'code' => 'commit_item.update'
            ],
            [
                'name' => '上传附件',
                'code' => 'commit_item.upload'
            ],
            [
                'name' => '删除附件',
                'code' => 'commit_item.upload_delete'
            ],
            [
                'name' => '数据排序',
                'code' => 'commit_item.order'
            ],
            [
                'name' => '删除数据',
                'code' => 'commit_item.delete'
            ]
        ]
    ],
    [
        'name' => '考核定义-实际测量项',
        'code' => 'commit_item_option.index',
        'children' => [
            [
                'name' => '获取列表',
                'code' => 'commit_item_option.list'
            ],
            [
                'name' => '保存数据',
                'code' => 'commit_item_option.save'
            ]
        ]
    ],
    [
        'name' => '任务单列表',
        'code' => 'task.index',
        'children' => [
            [
                'name' => '获取列表',
                'code' => 'task.list'
            ],
            [
                'name' => '添加任务',
                'code' => 'task.create'
            ],
            [
                'name' => '获取选项列表',
                'code' => 'task.option'
            ],
            [
                'name' => '删除任务',
                'code' => 'task.delete'
            ]
        ]
    ],
    [
        'name' => '定时任务',
        'code' => 'task_cron.index',
        'children' => [
            [
                'name' => '获取列表',
                'code' => 'task_cron.list'
            ],
            [
                'name' => '添加任务',
                'code' => 'task_cron.create'
            ],
            [
                'name' => '修改状态',
                'code' => 'task_cron.patch'
            ],
            [
                'name' => '删除任务',
                'code' => 'task_cron.delete'
            ]
        ]
    ],
    [
        'name' => '消息中心',
        'code' => 'notice.index',
        'children' => [
            [
                'name' => '获取列表',
                'code' => 'notice.list'
            ],
            [
                'name' => '添加信息',
                'code' => 'notice.create'
            ],
            [
                'name' => '查看详情',
                'code' => 'notice.detail'
            ],
            [
                'name' => '修改信息',
                'code' => 'notice.update'
            ],
            [
                'name' => '批量撤回',
                'code' => 'notice.retract'
            ],
            [
                'name' => '批量发布',
                'code' => 'notice.push'
            ],
            [
                'name' => '批量删除',
                'code' => 'notice.batch_delete'
            ]
        ]
    ],
    [
        'name' => '指导书中心',
        'code' => 'document.index',
        'children' => [
            [
                'name' => '获取列表',
                'code' => 'document.list'
            ],
            [
                'name' => '拆检指导书',
                'code' => 'document.overhaul'
            ],
            [
                'name' => '更新-拆检指导书',
                'code' => 'document.overhaul_update'
            ],
            [
                'name' => '装配指导书',
                'code' => 'document.assembling'
            ],
            [
                'name' => '更新-装配指导书',
                'code' => 'document.assembling_update'
            ],
            [
                'name' => '扭矩清单',
                'code' => 'document.torque'
            ],
            [
                'name' => '更新-扭矩清单',
                'code' => 'document.torque_update'
            ],
            [
                'name' => '删除信息',
                'code' => 'document.delete'
            ],
        ]
    ],
    [
        'name' => '发动机清单',
        'code' => 'product.index',
        'children' => [
            [
                'name' => '获取列表',
                'code' => 'product.list'
            ],
            [
                'name' => '添加信息',
                'code' => 'product.create'
            ],
            [
                'name' => '修改信息',
                'code' => 'product.update'
            ],
            [
                'name' => '删除信息',
                'code' => 'product.delete'
            ],
            [
                'name' => '导出数据',
                'code' => 'product.export'
            ],
            [
                'name' => '导入数据',
                'code' => 'product.import'
            ],
            [
                'name' => '下载模板',
                'code' => 'product.template'
            ]
        ]
    ],
    [
        'name' => '零件清单',
        'code' => 'part.index',
        'children' => [
            [
                'name' => '获取列表',
                'code' => 'part.list'
            ],
            [
                'name' => '添加信息',
                'code' => 'part.create'
            ],
            [
                'name' => '修改信息',
                'code' => 'part.update'
            ],
            [
                'name' => '删除信息',
                'code' => 'part.delete'
            ],
            [
                'name' => '零件列表',
                'code' => 'part.item'
            ],
            [
                'name' => '导入数据',
                'code' => 'part.import'
            ],
            [
                'name' => '下载模板',
                'code' => 'part.template'
            ]
        ]
    ]
];