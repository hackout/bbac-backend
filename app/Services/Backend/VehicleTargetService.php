<?php
namespace App\Services\Backend;

use Carbon\Carbon;
use App\Models\VehicleTarget;
use App\Services\Service;
use Illuminate\Support\Collection;

/**
 * 整车服务-问题日志服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class VehicleTargetService extends Service
{
    public ?string $className = VehicleTarget::class;

    public function getList(): array
    {
        $year = Carbon::today()->firstOfYear();
        parent::setQuery([
            ['yearly', '>=', $year->clone()->subYears(20)],
            ['yearly', '<=', $year],
        ]);
        $ebTypes = (new DictService())->getOptionByCode('eb_type')->toArray();
        $results = [];
        for($i = 0;$i < 30;$i++)
        {
            $item = [
                'yearly' => $year->clone()->subYears($i)->year
            ];
            parent::setQuery([
                ['yearly', '=', $year->clone()->subYears($i)]
            ]);
            $list = parent::getAll();
            foreach($ebTypes as $rs)
            {
                $item[$rs['value']] = optional($list->where('eb_type',$rs['value'])->first())->target ?? 0;
            }
            $results[] = $item;
        }
        return $results;
    }
    public function saveYearly(array $data)
    {
        $yearly = $data['yearly'];
        $eb_type = intval($data['eb_type']);
        $target = intval($data['target']);

        tap(parent::find(['yearly' => $yearly, 'eb_type' => $eb_type]), function (?VehicleTarget $item) use ($yearly, $eb_type, $target) {
            $sql = [
                'yearly' => $yearly,
                'eb_type' => $eb_type,
                'target' => $target,
            ];
            if ($item) {
                $item->fill($sql);
                $item->save();
            } else {
                parent::create([
                    'yearly' => $yearly,
                    'eb_type' => $eb_type,
                    'target' => $target,
                ]);
            }
        });
    }
}