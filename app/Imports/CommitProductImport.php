<?php

namespace App\Imports;

use App\Models\User;
use App\Models\CommitProduct;
use App\Services\Private\PartService;
use Illuminate\Support\Collection;
use App\Packages\Excel\ExcelDrawing;
use App\Services\Private\DictService;
use App\Services\Private\CommitProductService;
use Maatwebsite\Excel\Concerns\ToCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CommitProductImport implements ToCollection
{
    public function __construct(private User $user, private UploadedFile $uploadedFile)
    {
    }

    public function collection(Collection $rows)
    {
        $drawings = (new ExcelDrawing($this->uploadedFile))->toArray();
        $service = new DictService;
        $dicts = [
            'types' => $service->getOptionByCode('examine_product_item_type'),
            'parts' => (new PartService)->getOption()
        ];
        $name = trim($rows->get(0)->get(2));
        $engine = $service->getValueByCode('engine_type', trim($rows->get(1)->get(2)));
        $type = $service->getValueByCode('examine_product_type', trim($rows->get(1)->get(10)));
        $period = (float) trim($rows->get(1)->get(4));
        $version = trim($rows->get(1)->get(6));
        $lastVersion = trim($rows->get(1)->get(8));
        $parent = CommitProduct::where(['version' => $lastVersion, 'engine' => $engine, 'is_valid' => true])->first();
        $commitSql = [
            'author_id' => $this->user->id,
            'user_id' => $this->user->id,
            'examine_product_id' => optional($parent)->examine_product_id,
            'parent_id' => optional($parent)->id,
            'version' => $version,
            'name' => $name,
            'description' => null,
            'engine' => $engine,
            'period' => $period,
            'is_valid' => false,
            'status' => CommitProduct::STATUS_DRAFT,
            'type' => $type
        ];

        $itemSql = collect([]);
        $rows->each(function ($row, $index) use (&$itemSql, $dicts, $drawings) {
            if ($index > 2) {
                $row = array_pad($row->toArray(), 33, null);
                $sql = [
                    'part_id' => $dicts['parts']->where('name', trim($row[32]))->value('value'),
                    'name' => trim($row[10]),
                    'name_en' => trim($row[12]),
                    'content' => trim($row[2]),
                    'content_en' => trim($row[4]),
                    'standard' => trim($row[6]),
                    'standard_en' => trim($row[8]),
                    'eye' => trim($row[14]),
                    'eye_en' => trim($row[16]),
                    'number' => intval(trim($row[20])),
                    'lower_limit' => (float) trim($row[23]),
                    'upper_limit' => (float) trim($row[24]),
                    'unit' => trim($row[25]),
                    'torque' => trim($row[19]),
                    'is_scan' => trim($row[29]) == 'Y',
                    'is_camera' => trim($row[27]) == 'Y',
                    'is_ds' => trim($row[26]) == 'Y',
                    'scan' => trim($row[28]),
                    'camera' => trim($row[30]),
                    'record' => trim($row[21]),
                    'process' => intval(trim($row[31])),
                    'type' => $dicts['types']->where('name', trim($row[1]))->value('value') ?? 0,
                    'sort_order' => intval($row[0]),
                    'options' => [],
                    'medias' => isset ($drawings[$index][18]) ? $drawings[$index][18] : null
                ];
                for ($i = 0; $i < $sql['number']; $i++) {
                    $sql['options'][] = [
                        'name' => $i + 1,
                        'sort_order' => $i + 1
                    ];
                }
                if ($sql['type']) {
                    $itemSql->push($sql);
                }
            }
        });
        (new CommitProductService)->importData($commitSql, $itemSql->toArray());
    }
}
