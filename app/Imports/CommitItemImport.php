<?php

namespace App\Imports;

use App\Jobs\CommitItemImportJob;
use App\Models\Commit;
use App\Models\CommitItem;
use App\Services\Private\DictService;
use App\Services\Private\TorqueService;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CommitItemImport implements ToCollection
{

    public function __construct(private string $md5Key, private int $type, private int $sub_type)
    {
    }


    public function collection(Collection $rows)
    {
        $skip = 2;
        $newRows = [];
        $type = $this->type;
        $sub_type = $this->sub_type;
        $dictService = new DictService;
        $torques = (new TorqueService)->getOption();
        $newRows = $rows->filter(function ($n, $i) use ($skip) {
            return $i >= $skip && !empty ($n[0]);
        })->values()->map(function ($row) use ($type, $sub_type, $dictService, $torques) {
            $result = [
                'station' => null,
                'sub_station' => null,
                'name_zh' => null,
                'name_en' => null,
                'content_zh' => null,
                'content_en' => null,
                'standard_zh' => null,
                'standard_en' => null,
                'eye_zh' => null,
                'eye_en' => null,
                'number' => 1,
                'special' => 0,
                'gluing' => null,
                'bolt_number' => null,
                'bolt_model' => 0,
                'bolt_type' => 0,
                'bolt_status' => 0,
                'blot_close' => null,
                'lower_limit' => 0,
                'upper_limit' => 0,
                'unit' => null,
                'is_scan' => false,
                'is_camera' => false,
                'part_number' => null,
                'process' => 0,
                'type' => $sub_type,
                'sort_order' => 0,
            ];
            if ($sub_type == CommitItem::TYPE_TORQUE) {
                $result['station'] = trim($row[0]);
                $result['sub_station'] = trim($row[1]);
                $result['bolt_number'] = trim($row[2]);
                $result['content_zh'] = trim($row[3]);
                $result['content_en'] = trim($row[4]);
                $result['number'] = (int) $row[5];
                $result['bolt_model'] = $dictService->getValueByCode('bolt_model', trim($row[6]));
                $result['bolt_type'] = $dictService->getValueByCode('bolt_model', trim($row[7]));
                $result['special'] = $dictService->getValueByCode('special', trim($row[8]));
                $result['bolt_status'] = $dictService->getValueByCode('bolt_status', trim($row[9]));
                $result['lower_limit'] = (float) $row[10];
                $result['upper_limit'] = (float) $row[11];
                $result['unit'] = trim($row[12]);
                $result['name_zh'] = trim($row[13]);
                $result['name_en'] = trim($row[14]);
            }
            if ($sub_type == CommitItem::TYPE_DIMENSIONAL || $sub_type == CommitItem::TYPE_APPEARANCE || $sub_type == CommitItem::TYPE_PROCESS) {
                $result['station'] = trim($row[0]);
                $result['sub_station'] = trim($row[1]);
                $result['content_zh'] = trim($row[2]);
                $result['content_en'] = trim($row[3]);
                $result['standard_zh'] = trim($row[4]);
                $result['standard_en'] = trim($row[5]);
                $result['number'] = (int) $row[6];
                $result['special'] = $dictService->getValueByCode('special', trim($row[7]));
                $result['lower_limit'] = (float) $row[8];
                $result['upper_limit'] = (float) $row[9];
                $result['unit'] = trim($row[10]);
                $result['name_zh'] = trim($row[11]);
                $result['name_en'] = trim($row[12]);
            }
            if ($sub_type == CommitItem::TYPE_INK || $sub_type == CommitItem::TYPE_TEAR) {
                $result['station'] = trim($row[0]);
                $result['content_zh'] = trim($row[1]);
                $result['content_en'] = trim($row[2]);
                $result['standard_zh'] = trim($row[3]);
                $result['standard_en'] = trim($row[4]);
                $result['gluing'] = trim($row[5]);
                $result['number'] = (int) $row[6];
                $result['lower_limit'] = (float) $row[7];
                $result['upper_limit'] = (float) $row[8];
                $result['unit'] = trim($row[9]);
                $result['name_zh'] = trim($row[10]);
                $result['name_en'] = trim($row[11]);
            }
            if ($sub_type == CommitItem::TYPE_TRIAL || $sub_type == CommitItem::TYPE_TRIGGER || $sub_type == CommitItem::TYPE_PROJECT) {
                $result['station'] = trim($row[0]);
                $result['sub_station'] = trim($row[1]);
                $result['content_zh'] = trim($row[2]);
                $result['content_en'] = trim($row[3]);
                $result['standard_zh'] = trim($row[4]);
                $result['standard_en'] = trim($row[5]);
                $result['number'] = (int) $row[6];
                $result['special'] = $dictService->getValueByCode('special', trim($row[7]));
                $result['name_zh'] = trim($row[8]);
                $result['name_en'] = trim($row[9]);
            }
            if ($type == Commit::TYPE_PRODUCT && $sub_type == CommitItem::TYPE_MEASUREMENT) {
                $result['content_zh'] = trim($row[0]);
                $result['content_en'] = trim($row[1]);
                $result['standard_zh'] = trim($row[2]);
                $result['standard_en'] = trim($row[3]);
                $result['name_zh'] = trim($row[4]);
                $result['name_en'] = trim($row[5]);
                $result['bolt_close'] = trim($row[6]);
                $result['number'] = (int) $row[7];
                $result['lower_limit'] = (float) $row[8];
                $result['upper_limit'] = (float) $row[9];
                $result['unit'] = trim($row[10]);
                $result['is_scan'] = trim($row[11]) == 'YES';
                $result['is_camera'] = trim($row[12]) == 'YES';
                $result['part_number'] = $torques->where('name', trim($row[13]))->value('value');
                $result['process'] = (int) $row[14];
            }
            if ($type == Commit::TYPE_PRODUCT && $sub_type == CommitItem::TYPE_VISUAL) {
                $result['content_zh'] = trim($row[0]);
                $result['content_en'] = trim($row[1]);
                $result['standard_zh'] = trim($row[2]);
                $result['standard_en'] = trim($row[3]);
                $result['eye_zh'] = trim($row[4]);
                $result['eye_en'] = trim($row[5]);
                $result['bolt_close'] = trim($row[6]);
                $result['number'] = (int) $row[7];
                $result['lower_limit'] = (float) $row[8];
                $result['upper_limit'] = (float) $row[9];
                $result['unit'] = trim($row[10]);
                $result['is_scan'] = trim($row[11]) == 'YES';
                $result['is_camera'] = trim($row[12]) == 'YES';
                $result['part_number'] = $torques->where('name', trim($row[13]))->value('value');
                $result['process'] = (int) $row[14];
            }
            if ($type == Commit::TYPE_PRODUCT && $sub_type == CommitItem::TYPE_ALL) {
                $result['content_zh'] = trim($row[0]);
                $result['content_en'] = trim($row[1]);
                $result['standard_zh'] = trim($row[2]);
                $result['standard_en'] = trim($row[3]);
                $result['name_zh'] = trim($row[4]);
                $result['name_en'] = trim($row[5]);
                $result['name_zh'] = trim($row[6]);
                $result['name_en'] = trim($row[7]);
                $result['bolt_close'] = trim($row[8]);
                $result['number'] = (int) $row[9];
                $result['lower_limit'] = (float) $row[10];
                $result['upper_limit'] = (float) $row[11];
                $result['unit'] = trim($row[12]);
                $result['is_scan'] = trim($row[13]) == 'YES';
                $result['is_camera'] = trim($row[14]) == 'YES';
                $result['part_number'] = $torques->where('name', trim($row[15]))->value('value');
                $result['process'] = (int) $row[16];
            }
            if ($type == Commit::TYPE_VEHICLE && $sub_type == CommitItem::TYPE_MEASUREMENT) {
                $result['content_zh'] = trim($row[0]);
                $result['content_en'] = trim($row[1]);
                $result['standard_zh'] = trim($row[2]);
                $result['standard_en'] = trim($row[3]);
                $result['name_zh'] = trim($row[4]);
                $result['name_en'] = trim($row[5]);
                $result['lower_limit'] = (float) $row[6];
                $result['upper_limit'] = (float) $row[7];
                $result['unit'] = trim($row[8]);
                $result['is_scan'] = trim($row[9]) == 'YES';
                $result['is_camera'] = trim($row[10]) == 'YES';
                $result['part_number'] = $torques->where('name', trim($row[11]))->value('value');
                $result['process'] = (int) $row[12];
            }
            if ($type == Commit::TYPE_VEHICLE && $sub_type == CommitItem::TYPE_VISUAL) {
                $result['content_zh'] = trim($row[0]);
                $result['content_en'] = trim($row[1]);
                $result['standard_zh'] = trim($row[2]);
                $result['standard_en'] = trim($row[3]);
                $result['eye_zh'] = trim($row[4]);
                $result['eye_en'] = trim($row[5]);
                $result['lower_limit'] = (float) $row[6];
                $result['upper_limit'] = (float) $row[7];
                $result['unit'] = trim($row[8]);
                $result['is_scan'] = trim($row[9]) == 'YES';
                $result['is_camera'] = trim($row[10]) == 'YES';
                $result['part_number'] = $torques->where('name', trim($row[11]))->value('value');
                $result['process'] = (int) $row[12];
            }
            if ($type == Commit::TYPE_VEHICLE && $sub_type == CommitItem::TYPE_ALL) {
                $result['content_zh'] = trim($row[0]);
                $result['content_en'] = trim($row[1]);
                $result['standard_zh'] = trim($row[2]);
                $result['standard_en'] = trim($row[3]);
                $result['name_zh'] = trim($row[4]);
                $result['name_en'] = trim($row[5]);
                $result['name_zh'] = trim($row[6]);
                $result['name_en'] = trim($row[7]);
                $result['lower_limit'] = (float) $row[8];
                $result['upper_limit'] = (float) $row[9];
                $result['unit'] = trim($row[10]);
                $result['is_scan'] = trim($row[11]) == 'YES';
                $result['is_camera'] = trim($row[12]) == 'YES';
                $result['part_number'] = $torques->where('name', trim($row[13]))->value('value');
                $result['process'] = (int) $row[14];
            }
            return $result;
        })->toArray();
        CommitItemImportJob::dispatch($this->md5Key, $this->sub_type, $newRows)->delay(Carbon::now()->addMinute());
    }
}
