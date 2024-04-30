<?php

namespace App\Imports;

use App\Models\CommitVehicle;
use App\Packages\Excel\ExcelDrawing;
use App\Services\Private\CommitVehicleService;
use File;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Services\Private\DictService;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use App\Services\Private\CommitService;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CommitVehicleImport implements ToCollection
{
    public function __construct(private User $user, private UploadedFile $uploadedFile)
    {
    }

    public function collection(Collection $rows)
    {
        $drawings = (new ExcelDrawing($this->uploadedFile))->toArray();
        $service = new DictService;
        $name = trim($rows->get(0)->get(2));
        $engine = $service->getValueByCode('engine_type', trim($rows->get(1)->get(2)));
        $period = (float) trim($rows->get(1)->get(4));
        $version = trim($rows->get(1)->get(6));
        $lastVersion = trim($rows->get(1)->get(8));
        $parent = CommitVehicle::where(['version' => $lastVersion, 'engine' => $engine, 'is_valid' => true])->first();
        $types = $service->getOptionByCode('examine_vehicle_item_type');
        $commitSql = [
            'author_id' => $this->user->id,
            'user_id' => $this->user->id,
            'examine_vehicle_id' => optional($parent)->examine_vehicle_id,
            'parent_id' => optional($parent)->id,
            'version' => $version,
            'name' => $name,
            'description' => null,
            'engine' => $engine,
            'period' => $period,
            'is_valid' => false,
            'status' => CommitVehicle::STATUS_DRAFT
        ];

        $itemSql = collect([]);
        $rows->each(function ($row, $index) use (&$itemSql, $types, $drawings) {
            if ($index > 2) {
                $row = array_pad($row->toArray(), 8, null);
                $sql = [
                    'sort_order' => intval($row[0]),
                    'type' => $types->where('name', trim($row[1]))->value('value') ?? 0,
                    'content' => trim($row[2]),
                    'content_en' => trim($row[3]),
                    'standard' => trim($row[4]),
                    'standard_en' => trim($row[5]),
                    'other' => trim($row[6]),
                    'other_en' => trim($row[7]),
                    'medias' => isset ($drawings[$index][8]) ? $drawings[$index][8] : null
                ];
                if ($sql['type']) {
                    $itemSql->push($sql);
                }
            }
        });
        (new CommitVehicleService)->importData($commitSql,$itemSql->toArray());
    }
}
