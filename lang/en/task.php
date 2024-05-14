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
                'exists' => 'The parameter is incorrect.'
            ],
            'remark' => [
                'max' => 'The remark must be less than 250 characters.'
            ],
            'status' => [
                'required' => 'The status cannot be empty.',
                'in' => 'The status is incorrect.'
            ],
            'image' => [
                'required' => 'The image cannot be empty.',
                'image' => 'The image is incorrect.'
            ],
            'number' => [
                'required' => 'The number cannot be empty.',
                'array' => 'The number is incorrect.'
            ],
            'number_id' => [
                'required' => 'The number is incorrect.',
                'exists' => 'The number is incorrect.'
            ],
            'number_number' => [
                'required' => 'The number is incorrect.',
                'between' => 'The number is incorrect.'
            ],
        ]
    ],
];