<?php

namespace Database\Seeders;

use App\Models\SystemConfig;
use Illuminate\Database\Seeder;

class SystemConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = $this->getList();
        foreach($list as $rs)
        {
            SystemConfig::create($rs);
        }
    }

    private function getList()
    {
        return [
            [
                'code' => 'captcha_switch',
                'content' => 'off',
                'label' => '验证码开关',
                'type' => SystemConfig::TYPE_SWITCH
            ],
            [
                'code' => 'captcha_of_failed_switch',
                'content' => 'off',
                'label' => '登录失败验证码开关',
                'type' => SystemConfig::TYPE_SWITCH
            ],
            [
                'code' => 'fail_times_for_lock',
                'content' => '5',
                'label' => '登录失败次数限制',
                'type' => SystemConfig::TYPE_NUMERIC
            ],
            [
                'code' => 'default_period',
                'content' => '8',
                'label' => '基础工时',
                'type' => SystemConfig::TYPE_NUMERIC
            ]
        ];
    }
}
