<?php

return [
    'vehicle' => [
        'shift' => [
            'required' => '班次不能为空',
            'integer' => '班次不正确'
        ],
        'eb_type' => [
            'required' => '发动机/电池型号不能为空',
            'integer' => '发动机/电池型号不正确'
        ],
        'plant' => [
            'required' => '厂区不能为空',
            'integer' => '厂区不正确'
        ],
        'sensor_point' => [
            'required' => '问题发现点不能为空',
            'integer' => '问题发现点不正确'
        ],
        'car_series' => [
            'required' => '车系不能为空',
            'integer' => '车系不正确'
        ],
        'motorcycle_type' => [
            'required' => '车型不能为空',
            'integer' => '车型不正确'
        ],
        'pn_number' => [
            'required' => '生产订单号不能为空'
        ],
        'number' => [
            'required' => '发动机/电池号不能为空'
        ],
        'delay' => [
            'delay' => '滞留状态不正确'
        ],
        'description' => [
            'max' => '问题描述最大支持200个字符'
        ],
        'reason' => [
            'max' => '现场初步分析最大支持200个字符'
        ],
        'picture' => [
            'array' => '整体图片不正确',
            '*' => [
                'image' => '整体图片不正确'
            ]
        ],
        'image' => [
            'array' => '细节图片不正确',
            '*' => [
                'image' => '细节图片不正确'
            ]
        ],
        'video' => [
            'array' => '视频不正确',
            '*' => [
                'mimetypes' => '视频不正确'
            ]
        ],
        'issue' => [
            'exists' => '当前问题已反馈'
        ],
        'date' => [
            'array' => '时间参数不正确',
            '*' => [
                'date' => '时间参数不正确'
            ]
        ],
        'id' => [
            'exists_plus' => '参数不正确'
        ],
        'media' => [
            'array' => '附件信息不正确',
            '*' => [
                'uuid' => '附件信息不正确'
            ]
        ],
    ],
    'vehicle_block' => [
        'id' => [
            'exists_plus' => '信息错误'
        ],
        'block_status' => [
            'required' => '状态不能为空',
            'integer' => '状态错误'
        ],
        'block_content' => [
            'required_if' => '请选择一个检测类型',
            'integer' => '检测类型错误'
        ]
    ],
];