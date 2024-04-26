<?php
namespace App\Services\Backend;

use App\Models\User;
use App\Models\CommitApprove;
use App\Packages\CommitPlus\CommitPlus;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 版本送审服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitApproveService extends Service
{
    use ExportTemplateTrait;

    public ?string $className = CommitApprove::class;

    /**
     * 添加送审信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  array  $data
     * @return void
     */
    public function createByCommitId(User $user, string $id, array $data)
    {
        $sql = [
            'commit_id' => $id,
            'content' => array_key_exists('content', $data) ? trim($data['content']) : null,
            'influence' => array_key_exists('influence', $data) ? trim($data['influence']) : null,
            'concerns' => array_key_exists('concerns', $data) ? trim($data['concerns']) : null,
            'user_id' => $user->id,
            'messenger_id' => $user->id,
            'approver_id' => optional($user->department)->leader_id ?? $user->id,
            'extra' => (new CommitPlus((new CommitService)->findById($id)))->getChanged(),
            'description' => '',
            'status' => CommitApprove::STATUS_PENDING
        ];
        parent::create($sql);
    }

    /**
     * 审核变更
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitApprove $commitApprove
     * @param  boolean       $status
     * @param  string|null   $description
     * @return void
     */
    public function passApprove(CommitApprove $commitApprove,bool $status,?string $description = null)
    {
        if($commitApprove->status == CommitApprove::STATUS_PENDING)
        {
            $status = $status ? CommitApprove::STATUS_SUCCESS : CommitApprove::STATUS_REJECT;
            $sql = [
                'status' => $status,
                'description' => $description,
            ];
            parent::update($commitApprove->id,$sql);
        }
    }
}