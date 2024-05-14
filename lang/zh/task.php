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