<?php
namespace App\Services\Backend;

use App\Models\User;
use App\Models\Document;
use App\Services\Service;
use App\Packages\Department\DepartmentRole;
use Illuminate\Validation\ValidationException;

/**
 * 指导书服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DocumentService extends Service
{

    public ?string $className = Document::class;


    /**
     * 查询数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function getList(int $type, User $user): array
    {
        $result = [];
        if (!DepartmentRole::checkProduct($user)) {
            return $result;
        }
        $engineTypes = (new DictService)->getOptionByCode('engine_type');
        parent::setQuery(['type' => $type, 'is_valid' => true]);
        $items = parent::getAll()->map(function ($item) {
            $file = $item->getFirstMedia(Document::MEDIA_FILE);
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'user' => optional(optional($item->user)->profile)->name ?? optional($item->user)->number,
                'name' => $item->name,
                'size' => optional($file)->size ?? 0,
                'url' => optional($file)->getUrl() ?? null,
                'engine' => $item->engine,
                'is_valid' => $item->is_valid,
                'type' => $item->type,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });
        $engineTypes->each(function ($engine) use (&$result, $items, $type) {
            $item = $items->where('engine', $engine['value'])->first();
            $result[] = [
                'id' => $item ? $item['id'] : '',
                'user_id' => $item ? $item['user_id'] : '',
                'user' => $item ? $item['user'] : '',
                'name' => $item ? $item['name'] : '',
                'engine' => $item ? $item['engine'] : $engine['value'],
                'is_valid' => $item ? $item['is_valid'] : false,
                'type' => $item ? $item['type'] : $type,
                'size' => $item ? $item['size'] : 0,
                'url' => $item ? $item['url'] : null,
                'created_at' => $item ? $item['created_at'] : '',
                'updated_at' => $item ? $item['updated_at'] : ''
            ];
        });
        return $result;
    }

    /**
     * 删除指导书
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  User   $user
     * @return void
     * 
     * @throws ValidationException 
     */
    public function deleteFile(string $id,User $user)
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        parent::delete($id);
    }
}