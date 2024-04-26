<?php
namespace App\Services\Backend;

use App\Models\TorqueItem;
use App\Models\User;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Carbon\Carbon;

/**
 * 螺栓参数服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TorqueItemService extends Service
{
    use ExportTemplateTrait, ImportTemplateTrait;

    public ?string $className = TorqueItem::class;

    /**
     * 查询数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function getList(array $data): array
    {
        $conditions = [
            'keyword' => ['search', ['number', 'content_zh', 'content_en']],
            'plant' => 'eq',
            'line' => 'eq',
            'engine_type' => 'eq',
        ];
        parent::listQuery($data, $conditions);
        $result = parent::list();
        $months = $this->getMonthByYear();
        $result['items'] = $result['items']->map(function($item) use($months){
            $list = [
                'plant' => $item->torque->plant,
                'line' => $item->torque->line,
                'engine' => $item->torque->engine,
                'assembly_id' => $item->torque->assembly_id,
                'number' => $item->number,
                'content_zh' => $item->torque->content_zh,
                'content_en' => $item->torque->content_en,
                'months' => []
            ];
            $start = Carbon::now()->month(1)->day(1);
            $detail = new TorqueItemDetailService;
            $detail->setQuery([
                ['last_date','>=',$start]
            ]);
            $cps = $detail->getAll()->map(function($item){
                return [
                    'remark' => $item->remark,
                    'cp' => $item->cp,
                    'cpk' => $item->cpk,
                    'last_date' => $item->last_date->month
                ];
            });
            foreach($months as $rs)
            {
                $cp = $cps->where('last_date',$rs['name'])->first();
                if($cp)
                {
                    $list['months'][$rs['name']] = [
                        'remark' => $cp['remark'],
                        'cp' => $cp['cp'],
                        'cpk' => $cp['cpk']
                    ];
                }else{
                    $list['months'][$rs['name']] = [
                        'remark' => '',
                        'cp' => '',
                        'cpk' => ''
                    ];
                }
            }
            return $list;
        });
        return $result;
    }

    public function getMonthByYear():array
    {
        $result = [];
        for($i=1;$i < 13;$i++)
        {
            $result[] = [
                'name' => $i,
                'date' => Carbon::today()->month($i)->day(1)
            ];
        }
        return $result;
    }
}