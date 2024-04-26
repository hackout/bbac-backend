<?php

namespace App\Imports;

use App\Models\Commit;
use App\Services\Private\CommitService;
use App\Services\Private\DictService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class CommitImport implements ToCollection
{

    public function __construct(private string $md5Key, private int $type)
    {
    }

    public function collection(Collection $rows)
    {
        $service = new DictService;
        $engine = $service->getValueByCode('engine_type', trim($rows->get(3)->get(1)));
        $lastVersion = trim($rows->get(5)->get(1));
        $examine_id = null;
        $parent_id = null;
        if ($engine && $lastVersion) {
            $parent = Commit::where(['engine' => $engine, 'version' => $lastVersion, 'status' => Commit::STATUS_SUCCESS])->first();
            if ($parent) {
                $parent_id = $parent->id;
                $examine_id = $parent->examine_id;
            }
        }
        $sql = [
            'author_id' => request()->user()->id,
            'user_id' => request()->user()->id,
            'examine_id' => $examine_id,
            'parent_id' => $parent_id,
            'version' => trim($rows->get(4)->get(1)),
            'name' => trim($rows->get(1)->get(1)),
            'description' => trim($rows->get(7)->get(1)),
            'engine' => $engine,
            'period' => (int) $rows->get(6)->get(1),
            'is_valid' => true,
            'status' => Commit::STATUS_DRAFT,
            'type' => $this->type,
            'sub_type' => $service->getValueByCode('sub_type', trim($rows->get(2)->get(1))),
        ];
        $rules = [
            'name' => 'required',
            'type' => 'required|integer',
            'sub_type' => 'required|integer',
            'description' => 'max:200',
            'version' => 'required'
        ];

        $messages = [
            'name.required' => '模板名称不能为空',
            'type.required' => '模板类型不能为空',
            'type.integer' => '模板类型不存在',
            'sub_type.required' => '考核类型不能为空',
            'sub_type.integer' => '考核类型不存在',
            'version.required' => '版本号不能为空',
            'period.required' => '工时不能为空',
            'period.numeric' => '工时仅支持数字'
        ];

        $validator = Validator::make($sql, $rules, $messages);
        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $model = new CommitService;
        if ($model->create($sql)) {
            info('导入数据成功: ' . $model->item->id);
            Cache::put('import_' . $this->md5Key, $model->item->id);
        }
    }
}
