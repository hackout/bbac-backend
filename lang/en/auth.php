<?php
return [
    'login' => [
        'messages' => [
            'username' => [
                'required' => '用户名不能为空',
                'exists' => '登录名或密码错误'
            ],
            'password' => [
                'required' => '登录名或密码错误',
            ],
            'remember' => [
                'boolean' => '登录名或密码错误',
            ],
            'code' => [
                'captcha' => '验证码不正确',
            ],
        ]
    ]
];