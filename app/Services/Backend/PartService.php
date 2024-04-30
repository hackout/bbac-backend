<?php
namespace App\Services\Backend;

use App\Models\Part;
use App\Models\User;
use App\Packages\Department\DepartmentRole;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 零件清单
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class PartService extends Service
{
    use ExportTemplateTrait, ImportTemplateTrait;

    public ?string $className = Part::class;

    /**
     * 查询数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function getList(User $user, array $data): array
    {
        $result = [];
        if (!DepartmentRole::checkProduct($user)) {
            return $result;
        }
        $sql = [];
        if (array_key_exists('type', $data) && $data['type']) {
            switch ($data['type']) {
                case 'esd':
                    $sql[] = ['is_esd', '=', true];
                    break;
                case 'traceability':
                    $sql[] = ['is_traceability', '=', true];
                    break;
                case 'one_time':
                    $sql[] = ['is_one_time', '=', true];
                    break;
            }
        }
        if (array_key_exists('assembly', $data) && $data['assembly']) {
            $idList = ['-1'] + DB::table('parts_assemblies')->where('assembly_id', $data['assembly'])->get()->pluck('part_id')->toArray();
            $sql[] = [
                function ($query) use ($idList) {
                    $query->whereIn('id', $idList);
                }
            ];
        } else {
            if (array_key_exists('engine', $data) && $data['engine']) {
                $assemblies = ['-1'] + (new AssemblyService)->getIdListByEngine($data['engine']);
                $idList = ['-1'] + DB::table('parts_assemblies')->whereIn('assembly_id', $assemblies)->get()->pluck('part_id')->toArray();
                $sql[] = [
                    function ($query) use ($idList) {
                        $query->whereIn('id', $idList);
                    }
                ];
            }
        }
        if (array_key_exists('keyword', $data) && trim($data['keyword'])) {
            $keyword = trim($data['keyword']);
            $sql[] = [
                function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', "%$keyword%")
                        ->orWhere('number', 'LIKE', "%$keyword%")
                        ->orWhere('name_en', 'LIKE', "%$keyword%");
                }
            ];
        }
        if ($sql) {
            parent::setQuery($sql);
        }
        $result = parent::list();
        $result['items'] = $result['items']->map(function (Part $item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'name_en' => $item->name_en,
                'station' => $item->station,
                'number' => $item->number,
                'is_esd' => $item->is_esd,
                'is_traceability' => $item->is_traceability,
                'is_one_time' => $item->is_one_time,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'assemblies' => $item->assemblies->map(fn($n) => ['id' => $n->id, 'num' => $n->pivot->num, 'name' => $n->number, 'type' => $n->type])
            ];
        });
        return $result;
    }

    /**
     * 添加零件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  array $data
     * @return void
     * 
     * @throws ValidationException
     */
    public function createPart(User $user, array $data)
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $sql = [
            'name' => array_key_exists('name', $data) ? trim($data['name']) : null,
            'name_en' => array_key_exists('name_en', $data) ? trim($data['name_en']) : null,
            'station' => array_key_exists('station', $data) ? trim($data['station']) : null,
            'number' => array_key_exists('number', $data) ? trim($data['number']) : null,
            'is_esd' => array_key_exists('is_esd', $data) ? (bool) $data['is_esd'] : false,
            'is_traceability' => array_key_exists('is_traceability', $data) ? (bool) $data['is_traceability'] : false,
            'is_one_time' => array_key_exists('is_one_time', $data) ? (bool) $data['is_one_time'] : false,
        ];
        $assemblies = array_key_exists('assemblies', $data) && $data['assemblies'] ? $data['assemblies'] : [];
        if (parent::create($sql)) {
            foreach ($assemblies as $assembly) {
                $this->item->assemblies()->attach($assembly['id'], ['num' => $assembly['num']]);
            }
        }
    }

    /**
     * 更新零件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  string  $id
     * @param  array $data
     * @return void
     * 
     * @throws ValidationException
     */
    public function updatePart(User $user, string $id, array $data)
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $sql = [
            'name' => array_key_exists('name', $data) ? trim($data['name']) : null,
            'name_en' => array_key_exists('name_en', $data) ? trim($data['name_en']) : null,
            'station' => array_key_exists('station', $data) ? trim($data['station']) : null,
            'number' => array_key_exists('number', $data) ? trim($data['number']) : null,
            'is_esd' => array_key_exists('is_esd', $data) ? (bool) $data['is_esd'] : false,
            'is_traceability' => array_key_exists('is_traceability', $data) ? (bool) $data['is_traceability'] : false,
            'is_one_time' => array_key_exists('is_one_time', $data) ? (bool) $data['is_one_time'] : false,
        ];
        $assemblies = array_key_exists('assemblies', $data) && $data['assemblies'] ? $data['assemblies'] : [];
        if (parent::update($id, $sql)) {
            $this->item->assemblies()->detach();
            foreach ($assemblies as $assembly) {
                $this->item->assemblies()->attach($assembly['id'], ['num' => $assembly['num']]);
            }
        }
    }

    public function deletePart(string $id,User $user)
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        parent::delete($id);
    }

    /**
     * 导入零件清单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User         $user
     * @param  UploadedFile $file
     * @return void
     * 
     * @throws ValidationException
     */
    public function importByUser(User $user,UploadedFile $file)
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $this->import($file);
    }

    /**
     * 获取零件选项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return Collection
     */
    public function getOption(): Collection
    {
        return parent::getAll()->map(fn(Part $item) => ['value' => $item->id, 'name' => $item->number]);
    }
}