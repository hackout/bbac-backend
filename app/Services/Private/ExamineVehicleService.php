<?php
namespace App\Services\Private;

use App\Models\CommitVehicle;
use App\Models\ExamineVehicle;
use App\Services\Service;

/**
 * 整车服务-考核模板服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ExamineVehicleService extends Service
{

    public ?string $className = ExamineVehicle::class;

    /**
     * 同步审核版本信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitVehicle $commit
     * @return void
     */
    public function syncByCommit(CommitVehicle $commit)
    {
        $sql = [
            'author_id' => $commit->author_id,
            'user_id' => $commit->user_id,
            'commit_vehicle_id' => optional($commit->parent)->id,
            'version' => $commit->version,
            'name' => $commit->name,
            'description' => $commit->description,
            'engine' => $commit->engine,
            'period' => $commit->period,
            'is_valid' => true,
            'status' => $commit->status
        ];
        if ($commit->examine) {
            if (parent::update($commit->examine_vehicle_id, $sql)) {
                (new ExamineVehicleItemService)->updateByCommit($this->item, $commit);
            }
        } else {
            if (parent::create($sql)) {
                (new CommitVehicleService)->bindExamine($commit->id, $this->item->id);
                (new ExamineVehicleItemService)->createByCommit($this->item, $commit);
            }
        }
    }
}