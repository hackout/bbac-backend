<?php

namespace App\Imports;

use App\Models\Assembly;
use Illuminate\Support\Collection;
use App\Services\Private\PartService;
use Maatwebsite\Excel\Concerns\ToCollection;

class PartImport implements ToCollection
{

    public function collection(Collection $rows)
    {
        $header = $rows->first();
        $map = [
            'station' => 0,
            'name' => 1,
            'name_en' => 2,
            'number' => 3,
            'is_esd' => 4,
            'is_traceability' => 5,
            'is_one_time' => 6,
        ];
        $assemblies = [];
        $assemblyCount = $header->filter()->count() - count($map);
        if ($assemblyCount) {
            $map['is_esd'] += $assemblyCount;
            $map['is_traceability'] += $assemblyCount;
            $map['is_one_time'] += $assemblyCount;
            for ($i = 1; $i <= $assemblyCount; $i++) {
                $value = Assembly::where('number', $header[$i + 3])->value('id');
                if ($value) {
                    $assemblies[$value] = $i;
                }
            }
        }
        $rows->each(function ($row, $index) use ($assemblies, $map) {
            if ($index > 0) {
                $data = [
                    'station' => trim($row[$map['station']]),
                    'name' => trim($row[$map['name']]),
                    'name_en' => trim($row[$map['name_en']]),
                    'number' => trim($row[$map['number']]),
                    'is_esd' => trim($row[$map['is_esd']]) == 'Y',
                    'is_traceability' => trim($row[$map['is_traceability']]) == 'Y',
                    'is_one_time' => trim($row[$map['is_one_time']]) == 'Y',
                    'assemblies' => []
                ];
                if ($assemblies) {
                    foreach ($assemblies as $id => $key) {
                        $data['assemblies'][] = [
                            'id' => $id,
                            'num' => intval($row[$key])
                        ];
                    }
                }
                if($data['name'] && $data['number'])
                {
                    (new PartService)->importPart($data);
                }
            }
        });
    }
}