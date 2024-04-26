<?php
namespace App\Services\Private;

use App\Models\Assembly;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Illuminate\Support\Collection;

/**
 * 基础总成信息服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class AssemblyService extends Service
{
    use ExportTemplateTrait,ImportTemplateTrait;

    public ?string $className = Assembly::class;


    /**
     * 获取总成选项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getOptions(): Collection
    {
        return parent::getAll()->map(function($item){
            return [
                'value' => $item->id,
                'name' => $item->number
            ];
        });
    }
}