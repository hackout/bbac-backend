<?php
namespace App\Services\Backend;

use Carbon\Carbon;
use App\Models\VehicleOutbound;
use App\Services\Service;
use Illuminate\Support\Collection;

/**
 * 整车服务-问题日志服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class VehicleOutboundService extends Service
{
    public ?string $className = VehicleOutbound::class;

    public function getList(string $date): array
    {
        $month = Carbon::parse($date);
        parent::setQuery([
            ['daily', '>=', $month],
            ['daily', '<', $month->clone()->addMonth()],
        ]);
        $results = [];
        foreach (parent::getAll() as $rs) {
            if (!array_key_exists($rs->daily, $results)) {
                $results[$rs->daily] = [];
            }
            $results[$rs->daily][$rs->eb_type] = $rs->outbound;
        }
        return $results;
    }

    public function saveDaily(array $data)
    {
        $daily = Carbon::parse($data['daily']);
        $outBound = $data['outbound'];
        foreach ($outBound as $eb_type => $value) {
            tap(parent::find(['daily' => $daily, 'eb_type' => $eb_type]), function (?VehicleOutbound $item) use ($daily, $eb_type, $value) {
                if ($item) {
                    parent::setValue($item->id, 'outbound', $value);
                } else {
                    parent::create([
                        'daily' => $daily,
                        'outbound' => $value,
                        'eb_type' => $eb_type
                    ]);
                }
            });
        }
    }
}