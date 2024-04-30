<?php

namespace App\Imports;

use App\Models\User;
use App\Models\CommitInline;
use Illuminate\Support\Collection;
use App\Packages\Excel\ExcelDrawing;
use App\Services\Private\DictService;
use App\Services\Private\CommitInlineService;
use Maatwebsite\Excel\Concerns\ToCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CommitInlineImport implements ToCollection
{
    public function __construct(private User $user, private UploadedFile $uploadedFile)
    {
    }

    public function collection(Collection $rows)
    {
        $drawings = (new ExcelDrawing($this->uploadedFile))->toArray();
        $service = new DictService;
        $dicts = [
            'types' => $service->getOptionByCode('examine_inline_item_type'),
            'special' => $service->getOptionByCode('special'),
            'bolt_model' => $service->getOptionByCode('bolt_model'),
            'bolt_type' => $service->getOptionByCode('bolt_type'),
            'bolt_status' => $service->getOptionByCode('bolt_status')
        ];
        $name = trim($rows->get(0)->get(2));
        $engine = $service->getValueByCode('engine_type', trim($rows->get(1)->get(2)));
        $type = $service->getValueByCode('examine_inline_type', trim($rows->get(1)->get(10)));
        $period = (float) trim($rows->get(1)->get(4));
        $version = trim($rows->get(1)->get(6));
        $lastVersion = trim($rows->get(1)->get(8));
        $parent = CommitInline::where(['version' => $lastVersion, 'engine' => $engine, 'is_valid' => true])->first();
        $commitSql = [
            'author_id' => $this->user->id,
            'user_id' => $this->user->id,
            'examine_inline_id' => optional($parent)->examine_inline_id,
            'parent_id' => optional($parent)->id,
            'version' => $version,
            'name' => $name,
            'description' => null,
            'engine' => $engine,
            'period' => $period,
            'is_valid' => false,
            'status' => CommitInline::STATUS_DRAFT,
            'type' => $type
        ];

        $itemSql = collect([]);
        $rows->each(function ($row, $index) use (&$itemSql, $dicts, $drawings) {
            if ($index > 2) {
                $row = array_pad($row->toArray(), 19, null);
                $sql = [
                    'station' => trim($row[2]),
                    'name' => trim($row[3]),
                    'content' => trim($row[4]),
                    'content_en' => trim($row[5]),
                    'standard' => trim($row[6]),
                    'standard_en' => trim($row[7]),
                    'number' => intval($row[8]),
                    'special' => trim($row[14]) ? $dicts['special']->where('name', trim($row[14]))->value('value') : 0,
                    'gluing' => trim($row[12]),
                    'bolt_number' => trim($row[13]),
                    'bolt_model' => trim($row[16]) ? $dicts['bolt_model']->where('name', trim($row[16]))->value('value') : 0,
                    'bolt_type' => trim($row[17]) ? $dicts['bolt_type']->where('name', trim($row[17]))->value('value') : 0,
                    'bolt_status' => trim($row[18]) ? $dicts['bolt_status']->where('name', trim($row[18]))->value('value') : 0,
                    'lower_limit' => (float) trim($row[9]),
                    'upper_limit' => (float) trim($row[10]),
                    'unit' => trim($row[11]),
                    'type' => $dicts['types']->where('name', trim($row[1]))->value('value') ?? 0,
                    'sort_order' => intval($row[0]),
                    'options' => [],
                    'medias' => isset ($drawings[$index][15]) ? $drawings[$index][15] : null
                ];
                for($i = 0; $i < $sql['number'];$i++)
                {
                    $sql['options'][] = [
                        'name' => $sql['name']. ($i + 1),
                        'sort_order' => $i + 1
                    ];
                }
                if ($sql['type']) {
                    $itemSql->push($sql);
                }
            }
        });
        (new CommitInlineService)->importData($commitSql, $itemSql->toArray());
    }
}
