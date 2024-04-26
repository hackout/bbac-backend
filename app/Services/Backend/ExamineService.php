<?php
namespace App\Services\Backend;

use App\Models\Examine;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * 考核定义服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ExamineService extends Service
{
    use ExportTemplateTrait;

    public ?string $className = Examine::class;

    /**
     * 查询数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $type
     * @param  array $data
     * @return array
     */
    public function getList(array $data): array
    {
        $conditions = [
            'keyword' => ['search', ['name', 'description']],
            'sub_type' => 'eq',
            'type' => 'eq'
        ];
        parent::listQuery($data, $conditions);
        $result = parent::list();
        $result['items'] = $result['items']->map(function ($item) {
            $lastCommit = $item->commits->where('is_valid',false)->first();
            if(!$lastCommit)
            {
                $status = $item->status;
            }else{
                $status = $lastCommit->status;
            }
            return [
                'id' => $item->id,
                'author_id' => $item->author_id,
                'author' => optional(optional($item->author)->profile)->name ?? optional($item->author)->number,
                'user_id' => $item->user_id,
                'user' => optional(optional($item->user)->profile)->name ?? optional($item->user)->number,
                'last_version' => optional($item->lasted)->version,
                'version' => $item->version,
                'name' => $item->name,
                'description' => $item->description,
                'engine' => $item->engine,
                'period' => $item->period,
                'is_valid' => $item->is_valid,
                'status' => $status,
                'type' => $item->type,
                'sub_type' => $item->sub_type,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });
        return $result;
    }

    public function getOption(array $data)
    {
        $condition = [
            'type' => 'eq'
        ];
        parent::listQuery($data,$condition);
        return parent::getAll([
            'id as value',
            'name',
            'engine',
            'period',
            'version'
        ]);
    }

    public function updateByOld(string $id,array $data)
    {
        $examine = parent::findById($id);
        $lastCommit = $examine->commits->where('is_valid',false)->first();
        if(!$lastCommit)
        {
            $status = $examine->status;
        }else{
            $status = $lastCommit->status;
        }
        if($status)
        {
            throw ValidationException::withMessages(['examine_incorrect' => '该模板暂不可编辑']);
        }
        (new CommitService)->updateByLast($lastCommit,$data);
    }
}