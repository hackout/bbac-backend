<?php

return [
    'id_exists' => 'The issue does not exist.',
    'missing_permission' => 'No operation permissions.',
    'create' => [
        'shift' => [
            'required' => 'Shift cannot be empty.',
            'integer' => 'Shift is incorrect.'
        ],
        'eb_type' => [
            'required' => 'E&B-Type cannot be empty.',
            'integer' => 'E&B-Type is incorrect.'
        ],
        'plant' =>  [
            'required' => 'Plant cannot be empty.',
            'integer' => 'Plant is incorrect.'
        ],
        'sensor' =>  [
            'required' => 'Sensor cannot be empty.',
            'integer' => 'Sensor is incorrect.'
        ],
        'car_line' =>  [
            'required' => 'Car Line cannot be empty.',
            'integer' => 'Car Line is incorrect.'
        ],
        'car_type' =>  [
            'required' => 'V-Type cannot be empty.',
            'integer' => 'V-Type is incorrect.'
        ],
        'product_number' =>  [
            'required' => 'PN cannot be empty.'
        ],
        'eb_number' =>  [
            'required' => 'Engine/Battery SN cannot be empty.',
            'unique' => 'Engine/Battery SN does exists.',
        ],
        'is_block' => [
            'boolean' => 'Block status is incorrect.'
        ],
        'overview' => [
            'array' => 'Overview picture is incorrect.',
            '*' => [
                'image' => 'Overview picture is invalid.'
            ]
        ],
        'detail' => [
            'array' => 'Detail picture is incorrect.',
            '*' => [
                'image' => 'Detail picture is invalid.'
            ]
        ],
        'video' => [
            'array' => 'Video is incorrect.',
            '*' => [
                'mimetypes' => 'Video is invalid.'
            ]
        ]
    ],
    'list' => [
        'start' => [
            'date' => 'Start Date is invalid.'
        ],
        'end' => [
            'date' => 'End Date is invalid.'
        ]
    ],
    'update' => [
        'shift' => [
            'required' => 'Shift cannot be empty.',
            'integer' => 'Shift is incorrect.'
        ],
        'eb_type' => [
            'required' => 'E&B-Type cannot be empty.',
            'integer' => 'E&B-Type is incorrect.'
        ],
        'plant' =>  [
            'required' => 'Plant cannot be empty.',
            'integer' => 'Plant is incorrect.'
        ],
        'sensor' =>  [
            'required' => 'Sensor cannot be empty.',
            'integer' => 'Sensor is incorrect.'
        ],
        'car_line' =>  [
            'required' => 'Car Line cannot be empty.',
            'integer' => 'Car Line is incorrect.'
        ],
        'car_type' =>  [
            'required' => 'V-Type cannot be empty.',
            'integer' => 'V-Type is incorrect.'
        ],
        'product_number' =>  [
            'required' => 'PN cannot be empty.'
        ],
        'eb_number' =>  [
            'required' => 'Engine/Battery SN cannot be empty.',
            'unique' => 'Engine/Battery SN does exists.',
        ],
        'is_block' => [
            'boolean' => 'Block status is incorrect.'
        ],
        'overview' => [
            'array' => 'Overview picture is incorrect.',
            '*' => [
                'image' => 'Overview picture is invalid.'
            ]
        ],
        'detail' => [
            'array' => 'Detail picture is incorrect.',
            '*' => [
                'image' => 'Detail picture is invalid.'
            ]
        ],
        'video' => [
            'array' => 'Video is incorrect.',
            '*' => [
                'mimetypes' => 'Video is invalid.'
            ]
        ],
        'media' => [
            '*' => [
                'exists' => 'The attachment does not exist.'
            ]
        ]
    ],
    'poster' => [
        'uuid' => [
            'required' => 'The video screenshot is incorrect.',
            'exists' => 'The video screenshot is incorrect.'
        ],
        'poster' => [
            'required' => 'The video screenshot does not exist.',
        ]
    ]
];