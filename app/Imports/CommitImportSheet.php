<?php

namespace App\Imports;

use App\Models\Commit;
use App\Services\Private\DictService;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CommitImportSheet implements WithMultipleSheets, SkipsUnknownSheets
{

    public function __construct(private int $type)
    {
    }

    public function sheets(): array
    {
        $dicts = (new DictService)->getOptionByCode('examine_item_type');
        $key = md5(microtime());
        if ($this->type == Commit::TYPE_INLINE) {
            return [
                '主体' => new CommitImport($key, Commit::TYPE_INLINE),
                '触发考核' => new CommitItemImport($key, Commit::TYPE_INLINE, (int) $dicts->where('name', '触发考核')->value('value')),
                '项目支持' => new CommitItemImport($key, Commit::TYPE_INLINE, (int) $dicts->where('name', '项目支持')->value('value')),
                '试装支持' => new CommitItemImport($key, Commit::TYPE_INLINE, (int) $dicts->where('name', '试装支持')->value('value')),
                '扭矩监控' => new CommitItemImport($key, Commit::TYPE_INLINE, (int) $dicts->where('name', '扭矩监控')->value('value')),
                '尺寸测量' => new CommitItemImport($key, Commit::TYPE_INLINE, (int) $dicts->where('name', '尺寸测量')->value('value')),
                '外观检测' => new CommitItemImport($key, Commit::TYPE_INLINE, (int) $dicts->where('name', '外观检测')->value('value')),
                '过程监控' => new CommitItemImport($key, Commit::TYPE_INLINE, (int) $dicts->where('name', '过程监控')->value('value')),
                '墨水测试' => new CommitItemImport($key, Commit::TYPE_INLINE, (int) $dicts->where('name', '墨水测试')->value('value')),
                '撕胶测试' => new CommitItemImport($key, Commit::TYPE_INLINE, (int) $dicts->where('name', '撕胶测试')->value('value')),
            ];
        }
        if ($this->type == Commit::TYPE_PRODUCT) {
            return [
                '主体' => new CommitImport($key, Commit::TYPE_PRODUCT),
                '测量检查' => new CommitItemImport($key, Commit::TYPE_PRODUCT, (int) $dicts->where('name', '测量检查')->value('value')),
                '目视检查' => new CommitItemImport($key, Commit::TYPE_PRODUCT, (int) $dicts->where('name', '目视检查')->value('value')),
                '全部' => new CommitItemImport($key, Commit::TYPE_PRODUCT, (int) $dicts->where('name', '全部')->value('value'))
            ];
        }
        return [
            '主体' => new CommitImport($key, Commit::TYPE_VEHICLE),
            '测量检查' => new CommitItemImport($key, Commit::TYPE_VEHICLE, (int) $dicts->where('name', '测量检查')->value('value')),
            '目视检查' => new CommitItemImport($key, Commit::TYPE_VEHICLE, (int) $dicts->where('name', '目视检查')->value('value')),
            '全部' => new CommitItemImport($key, Commit::TYPE_VEHICLE, (int) $dicts->where('name', '全部')->value('value'))
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        info("跳过$sheetName");
    }
}
