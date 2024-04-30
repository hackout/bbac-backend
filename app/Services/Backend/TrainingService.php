<?php
namespace App\Services\Backend;

use App\Imports\TrainingImport;
use App\Models\TrainingUser;
use App\Models\Training;
use App\Packages\StoragePlus\StoragePlus;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 技能服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TrainingService extends Service
{
    use ImportTemplateTrait, ExportTemplateTrait;

    /**
     * 模板名
     */
    public $template;

    /**
     * 类型参数
     */
    protected $types = ['safe', 'skill', 'multiple'];

    public ?string $className = Training::class;

    /**
     * 获取类型字符串
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return string
     */
    public function getTypeString(): string
    {
        return implode(',', $this->types);
    }

    /**
     * 获取数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function getList(array $data): array
    {
        $conditions = [
            'keyword' => ['search', ['name']],
            'type' => 'eq',
            'date' => ['datetime_range', 'started_at'],
            'status' => 'eq'
        ];
        $data['type'] = array_search($data['type'], $this->types) + 1;
        parent::listQuery($data, $conditions);
        $result = parent::list();

        $department_id = array_key_exists('department_id', $data) && $data['department_id'] ? $data['department_id'] : '';
        $userIds = [];
        if ($department_id) {
            $userList = (new UserService)->getMemberByDepartment($department_id);
            if ($userList) {
                $userIds = collect($userList)->pluck('value')->all();
            }
        }
        $result['items'] = $result['items']->map(function ($item) use ($userIds) {
            $result = [
                'id' => $item->id,
                'name' => $item->name,
                'status' => $item->status,
                'type' => $item->type,
                'started_at' => $item->started_at,
                'period' => $item->period,
                'ended_at' => $item->ended_at,
                'attachments' => $item->getMedia(Training::MEDIA_FILE)->map(function ($item) {
                    return [
                        'type' => (new StoragePlus)->extensionToString($item->extension),
                        'uuid' => $item->uuid,
                        'name' => $item->file_name,
                        'url' => $item->original_url,
                        'size' => $item->size,
                        'extension' => $item->extension
                    ];
                }),
                'rate' => self::convertRate($item),
                'users' => []
            ];
            if ($userIds) {
                foreach ($userIds as $userId) {
                    $_user = optional($item->training_users)->where('user_id', $userId)->first();
                    if ($_user) {
                        $result['users'][$userId] = $_user->status;
                    } else {
                        $result['users'][$userId] = TrainingUser::STATUS_NONPARTICIPATION;
                    }
                }
            }
            return $result;
        });
        return $result;
    }

    /**
     * 计算培训完成度
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Training $training
     * @return float
     */
    private static function convertRate(Training $training): float
    {
        if (!$training->training_users)
            return 0;

        if ($training->type == Training::TYPE_SKILL) //技能培训计算
        {
            $total = $training->training_users->count() * 100;
            $count = $training->training_users->where('status', '!=', TrainingUser::STATUS_NOT_INVOLVED)->sum('status');
        } else {
            $total = $training->training_users->count();
            $count = $training->training_users->where('status', TrainingUser::STATUS_PASS)->count();
        }
        if(!$count || !$total) return 0;
        return (float) bcdiv($count, $total, 4) * 100;
    }

    /**
     * 根据类型获取导入模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $type
     * @return BinaryFileResponse
     */
    public function getTemplateByType(string $type): BinaryFileResponse
    {
        switch ($type) {
            case 'safe':
                $this->template = 'TrainingSafe';
                break;
            case 'skill':
                $this->template = 'TrainingSkill';
                break;
            case 'multiple':
                $this->template = 'TrainingMultiple';
                break;
        }
        return $this->downloadImportTemplate();
    }

    /**
     * 按类型导入数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $type
     * @param  UploadedFile $file
     * @return void
     */
    public function importByType(string $type, UploadedFile $file)
    {
        Excel::import(new TrainingImport(array_search($type, $this->types) + 1), $file);
    }

    /**
     * 上传附件到信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $type
     * @param  string       $id
     * @param  UploadedFile $file
     * @return void
     */
    public function uploadByTypeId(string $type, string $id, UploadedFile $file)
    {
        $item = parent::find([
            'type' => array_search($type, $this->types) + 1,
            'id' => $id
        ]);
        if($item)
        {
            $item->addMedia($file)->toMediaCollection(Training::MEDIA_FILE);
        }
    }

    /**
     * 删除附件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  string $file_uuid
     * @return void
     */
    public function deleteFile(string $id, string $file_uuid)
    {
        $item = Media::where([
            'model_id' => $id,
            'uuid' => $file_uuid,
            'model_type' => Training::class
        ])->first();
        if ($item) {
            $item->delete();
        }
    }

    /**
     * 快速修改培训信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  array  $data
     * @return bool
     */
    public function setItemValue(string $id, array $data): bool
    {
        if (array_key_exists('name', $data) && trim($data['name'])) {
            return parent::setValue($id, 'name', trim($data['name']));
        }
        if (array_key_exists('status', $data) && trim($data['status'])) {
            return parent::setValue($id, 'status', trim($data['status']));
        }
        if (array_key_exists('started_at', $data) && trim($data['started_at'])) {
            return parent::setValue($id, 'started_at', trim($data['started_at']));
        }
        if (array_key_exists('period', $data) && trim($data['period'])) {
            return parent::setValue($id, 'period', trim($data['period']));
        }
        if (array_key_exists('ended_at', $data) && trim($data['ended_at'])) {
            return parent::setValue($id, 'ended_at', trim($data['ended_at']));
        }
        return false;
    }
}