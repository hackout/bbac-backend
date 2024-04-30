<?php
namespace App\Services\Backend;

use App\Models\User;
use App\Models\Torque;
use App\Services\Service;
use App\Models\TorqueChangeRecord;
use Illuminate\Support\Collection;

/**
 * 扭矩数据服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TorqueChangeRecordService extends Service
{
    public ?string $className = TorqueChangeRecord::class;

    /**
     * 创建扭矩数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  array $data
     * @return void
     */
    public function createByUser(User $user, Torque $torque, array $data)
    {
        $sql = [
            'user_id' => $user->id,
            'torque_id' => $torque->id,
            'approver_id' => optional($user->department)->leader_id ?? $user->id,
            'is_io' => $data['is_io'],
            'status' => $data['is_io'] ? TorqueChangeRecord::STATUS_PENDING : TorqueChangeRecord::STATUS_SUCCESS,
            'extra' => []
        ];

        $torqueData = $torque->toArray();
        unset($data['is_io']);
        foreach ($data as $key => $value) {
            if ($value != $torqueData[$key]) {
                $sql['extra'][] = [
                    'field' => $key,
                    'before' => $torqueData[$key],
                    'content' => $value
                ];
            }
        }
        parent::create($sql);
    }

    /**
     * 获取变更记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string     $id
     * @return Collection
     */
    public function getListById(string $id): Collection
    {
        parent::setQuery(['torque_id' => $id]);
        $extraField = [
            'plant' => 'plant',
            'line' => 'assembly_line',
            'engine' => 'engine_type',
            'vehicle_type' => 'motorcycle_type',
            'model' => 'bolt_model',
            'type' => 'bolt_type',
            'status' => 'bolt_status',
            'stage' => 'assembly_status',
            'special' => 'special',
        ];
        $dictService = new DictService;
        $assemblyService = new AssemblyService;
        return parent::getAll()->map(function ($item) use($dictService,$assemblyService,$extraField) {
            return [
                'id' => $item->id,
                'user' => optional(optional($item->user)->profile)->name ?? optional($item->user)->number,
                'user_id' => $item->user_id,
                'approver' => optional(optional($item->approver)->profile)->name ?? optional($item->approver)->number,
                'approver_id' => $item->approver_id,
                'torque_number' => optional($item->torque)->number,
                'is_io' => $item->is_io,
                'status' => $item->status,
                'extra' => collect($item->extra)->map(function ($ext) use ($dictService, $assemblyService, $extraField) {
                    if (array_key_exists($ext['field'], $extraField)) {
                        $ext = [
                            'field' => $ext['field'],
                            'before' => $dictService->getNameByCode($extraField[$ext['field']], $ext['before']) ?? '',
                            'content' => $dictService->getNameByCode($extraField[$ext['field']], $ext['content']) ?? '',
                        ];
                    }
                    if ($ext['field'] == 'assembly_id') {
                        $before = $assemblyService->findById($ext['before']);
                        $content = $assemblyService->findById($ext['content']);
                        $ext = [
                            'field' => $ext['field'],
                            'before' => optional($before)->number,
                            'content' => optional($content)->number,
                        ];
                    }
                    return $ext;
                }),
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });
    }

    
    /**
     * 审核变更
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  TorqueChangeRecord $commitApprove
     * @param  boolean       $status
     * @param  string|null   $description
     * @return void
     */
    public function passApprove(TorqueChangeRecord $commitApprove,bool $status)
    {
        if($commitApprove->status == TorqueChangeRecord::STATUS_PENDING)
        {
            $status = $status ? TorqueChangeRecord::STATUS_SUCCESS : TorqueChangeRecord::STATUS_REJECT;
            $sql = [
                'status' => $status,
            ];
            parent::update($commitApprove->id,$sql);
        }
    }
}