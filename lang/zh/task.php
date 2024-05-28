<?php

return [
    'id_exists' => '考核不存在或已删除',
    'inline' => [
        'update' => [
            'id' => [
                'exists_plus' => '参数错误'
            ],
            'item_id' => [
                'required' => '参数错误',
                'exists_plus' => '参数错误'
            ],
            'content' => [
                'required' => '参数错误',
                'in' => '参数错误'
            ],

        ]
    ],
    'product' => [
        'update' => [
            'number' => [
                'required' => '请输入发动机号'
            ],
            'id' => [
                'required' => '参数错误',
                'exists' => '参数错误',
                'exists_plus' => '参数错误'
            ],
            'item_id' => [
                'required' => '参数错误',
                'exists_plus' => '参数错误'
            ],
            'content' => [
                'required' => '参数错误',
                'in' => '参数错误'
            ],
            'order' => [
                'integer' => '工序序号错误',
            ],
            'defect_level' => [
                'integer' => '缺陷等级错误',
            ],
            'defect_description' => [
                'integer' => '缺陷分类错误',
            ],
            'defect_part' => [
                'integer' => '问题零件错误',
            ],
            'defect_position' => [
                'integer' => '问题位置错误',
            ],
            'defect_cause' => [
                'integer' => '具体位置错误',
            ],
            'value' => [
                'array' => '具体数值错误',
            ],
            'files' => [
                'array' => '缺陷图片错误',
            ],
            'images' => [
                'array' => '拍摄图片错误',
                '*' => ['image' => '拍摄图片错误'],
            ],
        ],
        'detail' => [
            'id' => [
                'exists' => '参数错误'
            ],
            'assembled_at' => [
                'required' => '请输入装配时间',
                'date' => '装配时间不正确',
            ],
            'qc_at' => [
                'required' => '请输入热试时间',
                'date' => '热试时间不正确',
            ],
        ]
    ],
    'vehicle' => [
        'update' => [
            'id' => [
                'exists' => '参数错误'
            ],
            'remark' => [
                'max' => '备注信息最大支持250个字符'
            ],
            'status' => [
                'required' => '状态参数错误',
                'in' => '状态参数错误'
            ],
            'image' => [
                'required' => '检查记录不能为空',
                'image' => '检查记录不正确'
            ],
            'number' => [
                'required' => '检查数量不能为空',
                'array' => '检查数量不正确'
            ],
            'number_id' => [
                'required' => '检查数量不能为空',
                'exists' => '检查数量不正确'
            ],
            'number_number' => [
                'required' => '检查数量不能为空',
                'between' => '检查数量不正确'
            ],
        ]
    ],
];