<?php
namespace App\Services\Private;

use App\Models\CommitApprove;
use App\Models\Notice;
use App\Models\Task;
use App\Models\TorqueChangeRecord;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

/**
 * 消息通知服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class NoticeService extends Service
{

    public ?string $className = Notice::class;

    /**
     * 扭矩数据变更通知审核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  TorqueChangeRecord $torqueChangeRecord
     * @return void
     */
    public function makeNoticeByTorqueChanged(TorqueChangeRecord $torqueChangeRecord)
    {
        $sql = [
            'user_id' => $torqueChangeRecord->user_id,
            'title' => 'IO变更审核',
            'type' => Notice::APPROVE,
            'content' => null,
            'from' => null,
            'model_id' => $torqueChangeRecord->id,
            'model_type' => get_class($torqueChangeRecord),
            'is_sent' => false,
            'is_valid' => true,
            'extra' => $torqueChangeRecord->extra,
        ];
        if (parent::create($sql)) {
            DB::table("users_notices")->insert([
                'user_id' => $torqueChangeRecord->approver_id,
                'notice_id' => $this->item->id,
                'is_read' => 0
            ]);
        }
    }

    /**
     * 创建考核审核推送
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitApprove $commitApprove
     * @return void
     */
    public function makeNoticeByCommitApprove(CommitApprove $commitApprove)
    {
        $sql = [
            'user_id' => $commitApprove->user_id,
            'title' => '考核变更审核',
            'type' => Notice::APPROVE,
            'content' => null,
            'from' => null,
            'model_id' => $commitApprove->id,
            'model_type' => get_class($commitApprove),
            'is_sent' => false,
            'is_valid' => true,
            'extra' => $commitApprove->extra,
        ];
        if (parent::create($sql)) {
            DB::table("users_notices")->insert([
                'user_id' => $commitApprove->approver_id,
                'notice_id' => $this->item->id,
                'is_read' => 0
            ]);
        }
    }

    /**
     * 创建任务推送
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Task $task
     * @return void
     */
    public function makeNoticeByTask(Task $task)
    {
        $sql = [
            'user_id' => $task->user_id,
            'title' => $task->name,
            'type' => Notice::APPROVE,
            'content' => null,
            'from' => null,
            'model_id' => $task->id,
            'model_type' => get_class($task),
            'is_sent' => false,
            'is_valid' => true,
            'extra' => $task->original_examine,
        ];
        if (parent::create($sql)) {
            DB::table("users_notices")->insert([
                'user_id' => $task->user_id,
                'notice_id' => $this->item->id,
                'is_read' => 0
            ]);
        }
    }

    /**
     * 推送消息到部门
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Notice $notice
     * @return void
     */
    public function push(Notice $notice)
    {
        if ($user = $notice->user) {
            if ($department = $user->department) {
                $toUsers = (new DepartmentService)->getAllUserByDepartment($department);
                if ($toUsers) {
                    $notice->users()->attach($toUsers, ['is_read' => 0]);
                }
            }
        }
        $notice->fill(['is_sent' => true, 'is_valid' => true]);
        if ($notice->save()) {
            $this->clearCache();
        }
    }

    /**
     * 撤销所有推送
     */
    public function retract(Notice $notice)
    {
        if ($user = $notice->user) {
            $notice->users()->detach();
        }
        $notice->fill(['is_sent' => false]);
        if ($notice->save()) {
            $this->clearCache();
        }
    }
}