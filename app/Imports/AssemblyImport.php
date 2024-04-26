<?php

namespace App\Imports;

use App\Models\Assembly;
use App\Services\Private\DictService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToCollection;

class AssemblyImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $dictService = new DictService;
        $dicts = [
            'assembly_status' => $dictService->getOptionByCode('assembly_status'),
            'assembly_line' => $dictService->getOptionByCode('assembly_line'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'plant' => $dictService->getOptionByCode('plant')
        ];
        $collection->each(function ($row, $index) use ($dicts) {
            if ($index > 1) {
                list ($type, $plant, $line, $status, $number, $remark) = array_pad($row->toArray(), 6, null);
                $type = $dicts['engine_type']->where('name', trim($type))->value('value');
                $plant = $dicts['plant']->where('name', trim($plant))->value('value');
                $line = $dicts['assembly_line']->where('name', trim($line))->value('value');
                $status = $dicts['assembly_status']->where('name', trim($status))->value('value');
                $number = trim($number);
                $remark = trim($remark);
                if ($type !== null && $plant !== null && $line !== null && $status !== null && $number) {
                    Assembly::updateOrCreate([
                        'number' => $number
                    ], [
                        'type' => $type,
                        'plant' => $plant,
                        'line' => $line,
                        'status' => $status,
                        'number' => trim($number),
                        'remark' => trim($remark)
                    ]);
                }
            }
        });
        $cacheName = (new Assembly)->getTable();
        $cache = Cache::get($cacheName, []);
        foreach ($cache as $key) {
            Cache::forget($key);
        }
        Cache::forget($cacheName);
    }
}
