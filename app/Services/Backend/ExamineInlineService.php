<?php
namespace App\Services\Backend;

use App\Services\Service;
use App\Models\ExamineInline;

/**
 * 在线考核-模板服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ExamineInlineService extends Service
{

    public ?string $className = ExamineInline::class;

}