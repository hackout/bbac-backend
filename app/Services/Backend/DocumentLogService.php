<?php
namespace App\Services\Backend;

use App\Jobs\SyncDocumentMedia;
use App\Models\User;
use App\Services\Service;
use Illuminate\Support\Str;
use App\Models\DocumentLog;
use App\Packages\Department\DepartmentRole;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 指导书服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DocumentLogService extends Service
{

    public ?string $className = DocumentLog::class;

    /**
     * 更新拆检指导书
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  integer      $engine
     * @param  User         $user
     * @param  UploadedFile $file
     * @return array
     */
    public function overhaulUpdate(int $engine, User $user, UploadedFile $file): array
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $document = (new DocumentService)->find(['engine' => $engine, 'type' => DocumentLog::TYPE_OVERHAUL]);
        $sql = [
            'user_id' => $user->id,
            'document_id' => optional($document)->id,
            'name' => $file->getClientOriginalName(),
            'engine' => $engine,
            'is_valid' => true,
            'type' => DocumentLog::TYPE_OVERHAUL,
        ];
        parent::create($sql);
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $media = $this->item->addMedia($file)->usingName($fileName)->toMediaCollection(DocumentLog::MEDIA_FILE);
        SyncDocumentMedia::dispatch($this->item);
        $result = [
            'url' => $media->getUrl(),
            'alt' => $sql['name'],
            'href' => $media->getUrl()
        ];
        return $result;
    }

    /**
     * 更新装配指导书
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  integer      $engine
     * @param  User         $user
     * @param  UploadedFile $file
     * @return array
     */
    public function assemblingUpdate(int $engine, User $user, UploadedFile $file): array
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $document = (new DocumentService)->find(['engine' => $engine, 'type' => DocumentLog::TYPE_ASSEMBLING]);
        $sql = [
            'user_id' => $user->id,
            'document_id' => optional($document)->id,
            'name' => $file->getClientOriginalName(),
            'engine' => $engine,
            'is_valid' => true,
            'type' => DocumentLog::TYPE_ASSEMBLING,
        ];
        parent::create($sql);
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $media = $this->item->addMedia($file)->usingName($fileName)->toMediaCollection(DocumentLog::MEDIA_FILE);
        SyncDocumentMedia::dispatch($this->item);
        $result = [
            'url' => $media->getUrl(),
            'alt' => $sql['name'],
            'href' => $media->getUrl()
        ];
        return $result;
    }

    /**
     * 更新扭矩清单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  integer      $engine
     * @param  User         $user
     * @param  UploadedFile $file
     * @return array
     */
    public function torqueUpdate(int $engine, User $user, UploadedFile $file): array
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $document = (new DocumentService)->find(['engine' => $engine, 'type' => DocumentLog::TYPE_TORQUE]);
        $sql = [
            'user_id' => $user->id,
            'document_id' => optional($document)->id,
            'name' => $file->getClientOriginalName(),
            'engine' => $engine,
            'is_valid' => true,
            'type' => DocumentLog::TYPE_TORQUE,
        ];
        parent::create($sql);
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $media = $this->item->addMedia($file)->usingName($fileName)->toMediaCollection(DocumentLog::MEDIA_FILE);
        SyncDocumentMedia::dispatch($this->item);
        $result = [
            'url' => $media->getUrl(),
            'alt' => $sql['name'],
            'href' => $media->getUrl()
        ];
        return $result;
    }

}