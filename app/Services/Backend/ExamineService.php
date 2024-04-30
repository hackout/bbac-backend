<?php
namespace App\Services\Backend;

use App\Models\User;
use App\Models\ExamineInline;
use App\Models\ExamineProduct;
use App\Models\ExamineVehicle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Packages\Department\DepartmentRole;
use Illuminate\Validation\ValidationException;


/**
 * 版本送审服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ExamineService
{
    public function getList(User $user, array $data): array
    {
        $permission = [];
        if (DepartmentRole::checkProduct($user)) {
            $permission[] = 'product';
        }
        if (DepartmentRole::checkInline($user)) {
            $permission[] = 'inline';
        }
        if (DepartmentRole::checkVehicle($user)) {
            $permission[] = 'vehicle';
        }
        $result = [
            'total' => 0,
            'items' => collect([])
        ];
        if (!$permission) {
            return $result;
        }
        $sql = [
            ['is_valid', '=', true]
        ];
        if (array_key_exists('keyword', $data) && trim($data['keyword'])) {
            $keyword = trim($data['keyword']);
            $sql[] = [
                function ($query) use ($keyword) {
                    $query->where('version', 'LIKE', "%$keyword%")
                        ->orWhere('name', 'LIKE', "%$keyword%")
                        ->orWhere('description', 'LIKE', "%$keyword%");
                }
            ];
        }
        if (array_key_exists('status', $data) && trim($data['status'])) {
            $sql[] = ['status', '=', intval($data['status'])];
        }
        $limit = array_key_exists('limit', $data) && $data['limit'] ? intval($data['limit']) : 20;
        $page = array_key_exists('page', $data) && $data['page'] ? intval($data['page']) : 1;
        $type = array_key_exists('type', $data) && $data['type'] ? trim($data['type']) : null;
        $skip = ($page - 1) * $limit;
        $unionAll = [];
        if ((!$type || $type == 'inline') && in_array('inline', $permission)) {
            $unionAll[] = DB::table((new ExamineInline)->getTable())->where($sql)->select(DB::raw("id,created_at,'inline' as model"));
        }
        if ((!$type || $type == 'product') && in_array('product', $permission)) {
            $unionAll[] = DB::table((new ExamineProduct)->getTable())->where($sql)->select(DB::raw("id,created_at,'product' as model"));
        }
        if ((!$type || $type == 'vehicle') && in_array('vehicle', $permission)) {
            $unionAll[] = DB::table((new ExamineVehicle)->getTable())->where($sql)->select(DB::raw("id,created_at,'vehicle' as model"));
        }
        $firstDB = $unionAll[0];
        if (count($unionAll) > 1) {
            $masterDB = $firstDB->unionAll($unionAll[1])->unionAll($unionAll[2]);
        } else {
            $masterDB = $firstDB;
        }
        $result['total'] = $masterDB->count();
        $list = $masterDB->orderBy('created_at', 'DESC')->skip($skip)->take($limit)->get();
        foreach ($list as $rs) {
            switch ($rs->model) {
                case 'inline':
                    $item = ExamineInline::find($rs->id);
                    break;
                case 'product':
                    $item = ExamineProduct::find($rs->id);
                    break;
                default:
                    $item = ExamineVehicle::find($rs->id);
                    break;
            }
            $result['items']->push([
                'id' => $item->id,
                'name' => $item->name,
                'user_id' => $item->user_id,
                'user' => optional(optional($item->user)->profile)->name ?? optional($item->user)->number,
                'author_id' => $item->author_id,
                'author' => optional(optional($item->author)->profile)->name ?? optional($item->author)->number,
                'engine' => $item->engine,
                'version' => $item->version,
                'last_version' => optional($item->commit)->version,
                'description' => $item->description,
                'status' => $item->status,
                'type' => $item->type,
                'period' => (float) $item->period,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'model' => $rs->model
            ]);
        }
        return $result;
    }

    /**
     * 删除考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $type
     * @param  string $id
     * @return void
     * 
     * @throws ValidationException
     */
    public function delete(User $user, string $type, string $id)
    {
        switch ($type) {
            case 'inline':
                if (!DepartmentRole::checkInline($user)) {
                    throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
                }
                $item = ExamineInline::find($id);
                break;
            case 'product':
                if (!DepartmentRole::checkProduct($user)) {
                    throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
                }
                $item = ExamineProduct::find($id);
                break;
            default:
                if (!DepartmentRole::checkVehicle($user)) {
                    throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
                }
                $item = ExamineVehicle::find($id);
                break;
        }
        $item->delete();
    }

    /**
     * 获取考核选项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User       $user
     * @param  string     $type
     * @return Collection
     */
    public function getOption(User $user, string $type): Collection
    {
        switch ($type) {
            case 'inline':
                if (!DepartmentRole::checkInline($user)) {
                    return collect([]);
                }
                $items = ExamineInline::where(['is_valid' => true, 'status' => ExamineInline::STATUS_SUCCESS])->select('id', 'name', 'version','engine', 'type','period')->get();
                break;
            case 'product':
                if (!DepartmentRole::checkProduct($user)) {
                    return collect([]);
                }
                $items = ExamineProduct::where(['is_valid' => true, 'status' => ExamineProduct::STATUS_SUCCESS])->select('id', 'name', 'version','engine', 'type','period')->get();
                break;
            default:
                if (!DepartmentRole::checkVehicle($user)) {
                    return collect([]);
                }
                $items = ExamineVehicle::where(['is_valid' => true, 'status' => ExamineVehicle::STATUS_SUCCESS])->select('id', 'name', 'version','engine','period')->get();
                break;
        }
        return $items->map(fn($item) => ['value' => $item->id, 'name' => $item->name, 'version' => $item->version, 'engine' => $item->engine, 'period' => $item->period]);
    }
}