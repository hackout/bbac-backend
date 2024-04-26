<?php
namespace App\Packages\LogWrite;

class RouteMaps
{

    private static $maps = [
        'GET' => [
            'home' => '访问后台首页',
            'login' => '访问登录页',
            'forget' => '访问忘记密码',
            'login.first' => '访问完善信息页',
            'profile' => '访问个人中心',
            'profile.log' => '访问个人日志',
            'member' => '访问组织机构维护',
            'member.role' => '访问角色维护',
            'member.member' => '访问员工维护',
            'member.safe' => '访问安全培训',
            'member.skill' => '访问技能培训',
            'member.other' => '访问其他培训',
            'member.birthday' => '访问生日祝福',
        ],
        'POST' => [
            'login' => '登录后台',
            'logout' => '退出登录',
            'forget.reset' => '重置登录密码',
            'forget.send' => '获取验证码',
            'forget.check' => '检查账号',
            'login.first' => '完善个人信息',
            'check.password' => '二次校验密码',
            'uploadImage' => '上传图片',
            'uploadVideo' => '上传视频',
            'profile' => '修改个人资料',
            'profile.account' => '修改账号信息',
            'profile.password' => '修改登录密码',
        ],
        'DELETE' => [

        ],
        'PATCH' => [

        ],
        'PUT' => [

        ]
    ];
    public static function getMaps()
    {
        return self::$maps;
    }

}