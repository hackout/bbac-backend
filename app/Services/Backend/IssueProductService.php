<?php
namespace App\Services\Backend;

use Str;
use Storage;
use FFMpeg\FFMpeg;
use App\Models\Task;
use App\Models\User;
use App\Services\Service;
use App\Models\IssueProduct;
use FFMpeg\Coordinate\TimeCode;
use App\Packages\Department\DepartmentRole;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 整车服务-问题服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class IssueProductService extends Service
{
    public ?string $className = IssueProduct::class;

    /**
     * 获取记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return array
     */
    public function getListByProduct(User $user, string $id): array
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $lists = parent::setQuery(['task_id' => $id])->getAll();

        return $lists->map(function (IssueProduct $issueProduct) {
            return [
                'id' => $issueProduct->id,
                'eb_number' => optional($issueProduct->task)->eb_number,
                'defect_description' => $issueProduct->defect_description,
                'defect_level' => $issueProduct->defect_level,
                'defect_part' => $issueProduct->defect_part,
                'defect_position' => $issueProduct->defect_position,
                'defect_cause' => $issueProduct->defect_cause,
                'soma' => $issueProduct->soma,
                'lama' => $issueProduct->lama,
                'note' => $issueProduct->note,
                'cause' => $issueProduct->cause,
                'eight_disciplines' => $issueProduct->eight_disciplines,
                'score_card' => $issueProduct->score_card,
                'ira' => $issueProduct->ira,
                'department' => $issueProduct->department,
                'status' => $issueProduct->status,
                'type' => $issueProduct->type,
                'is_ok' => $issueProduct->is_ok,
                'defect_attaches' => $issueProduct->defect_attaches,
                'attachments' => $issueProduct->attachments
            ];
        })->toArray();
    }

    /**
     * 获取记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  array $data
     * @return array
     */
    public function getList(User $user, array $data): array
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }

        $conditions = [
            'keyword' => ['search', ['eb_number', 'note']],
            'date' => ['datetime_range', 'created_at'],
            'user_id' => ['eq', 'author_id'],
            'status' => 'eq'
        ];
        parent::listQuery($data, $conditions);
        $result = parent::list();
        $result['items'] = $result['items']->map(function (IssueProduct $item) {
            return [
                'id' => $item->id,
                'author_id' => $item->author_id,
                'author' => optional(optional($item->author)->profile)->name ?? optional($item->author)->number,
                'user_id' => $item->user_id,
                'user' => optional(optional($item->user)->profile)->name ?? optional($item->user)->number,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'stage' => $item->stage,
                'eb_number' => optional($item->task)->eb_number,
                'defect_description' => $item->defect_description,
                'defect_level' => $item->defect_level,
                'defect_part' => $item->defect_part,
                'defect_position' => $item->defect_position,
                'defect_cause' => $item->defect_cause,
                'cause' => $item->cause,
                'soma' => $item->soma,
                'lama' => $item->lama,
                'note' => $item->note,
                'eight_disciplines' => $item->eight_disciplines,
                'ira' => $item->ira,
                'score_card' => $item->score_card,
                'department' => $item->department,
                'status' => $item->status,
                'type' => $item->type,
                'is_ok' => $item->is_ok,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'defect_attaches' => $item->defect_attaches,
                'attachments' => $item->attachments
            ];
        });
        return $result;
    }
    /**
     * 修改记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @param  array  $data
     * @return void
     */
    public function updateIssue(User $user, string $id, array $data): void
    {
        if (!DepartmentRole::checkVehicle($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        if (array_key_exists('file', $data) && $data['file']) {
            if ($data['file'] instanceof UploadedFile) {
                $file = $data['file'];
            }
            unset($data['file']);
        }
        if (parent::update($id, $data)) {
            if (array_key_exists('score_card', $data) && $data['score_card'] && $this->item->task) {
                tap((new TaskService())->findById($this->item->task_id), function (Task $task) {
                    $score_card = IssueProduct::where('task_id', $task->id)->sum('score_card');
                    $extra = $task->extra;
                    $extra['score'] = $score_card;
                    (new TaskService())->update($task->id, $extra);
                });
            }
            if (isset($file)) {
                $this->item->addMedia($file)->toMediaCollection(IssueProduct::MEDIA_FILE);
            }
        }
    }

    /**
     * 删除记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return void
     */
    public function deleteIssue(User $user, string $id): void
    {
        if (!DepartmentRole::checkVehicle($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        parent::delete($id);
    }
}