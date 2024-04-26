<?php
namespace App\Services\Backend;

use App\Models\Role;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * 角色服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class RoleService extends Service
{
    use ImportTemplateTrait, ExportTemplateTrait;

    public ?string $className = Role::class;

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
            'keyword' => ['search', ['name']]
        ];
        parent::listQuery($data, $conditions);
        return parent::list([
            'id',
            'name',
            'is_valid',
            'user_count',
            'permissions'
        ]);
    }

    /**
     * 获取可用权限String字符串
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return string
     */
    public function getPermissions(): string
    {
        $data = config('permission');
        $result = [];
        foreach ($data as $rs) {
            $result[] = $rs['code'];
            if (array_key_exists('children', $rs) && $rs['children']) {
                foreach ($rs['children'] as $son) {
                    $result[] = $son['code'];
                }
            }
        }
        return implode(',', $result);
    }

    /**
     * 角色启用停用
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  integer|string $id
     * @return void
     */
    public function valid(int|string $id)
    {
        $item = self::findById($id);
        if (!$item) {
            throw ValidationException::withMessages(['id.not_exists' => '角色不存在']);
        }
        parent::setValue($id, 'is_valid', !$item->is_valid);
    }

    /**
     * 获取所有角色
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return Collection
     */
    public function getOptions():Collection
    {
        return parent::getAll([
            'id as value',
            'name'
        ]);
    }
}