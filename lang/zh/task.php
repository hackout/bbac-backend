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
];