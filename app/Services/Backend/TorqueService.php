<?php
namespace App\Services\Backend;

use App\Models\Torque;
use App\Models\User;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 扭矩数据服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TorqueService extends Service
{
    use ExportTemplateTrait, ImportTemplateTrait;

    public ?string $className = Torque::class;

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
        return parent::list();
    }

    /**
     * 更新扭矩数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @param  array  $data
     * @return void
     */
    public function updateByUser(User $user, string $id, array $data)
    {
        $torque = parent::findById($id);
        (new TorqueChangeRecordService)->createByUser($user, $torque, $data);
    }

    /**
     * 导入数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User         $user
     * @param  UploadedFile $file
     * @return void
     */
    public function importByUser(User $user, UploadedFile $file)
    {
        $import = $this->getImportClassName();
        Excel::import(new $import($user,$file), $file);
    }

    public function getOptions()
    {
        return parent::getAll([
            'id as value',
            'number as name'
        ]);
    }
}