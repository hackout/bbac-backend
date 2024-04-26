<?php
namespace App\Services\Frontend;

use App\Models\Assembly;
use App\Services\Service;
use Illuminate\Database\Eloquent\Model;

/**
 * 总成服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class AssemblyService extends Service
{
    public ?string $className = Assembly::class;


}