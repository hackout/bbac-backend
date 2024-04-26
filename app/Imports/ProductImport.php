<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Services\Private\DictService;
use App\Services\Private\ProductService;
use App\Services\Private\AssemblyService;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductImport implements ToCollection
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
            'plant' => $dictService->getOptionByCode('plant'),
            'assemblies' => (new AssemblyService)->getOptions()
        ];
        $collection->each(function ($row, $index) use ($dicts) {
            if ($index) {
                list ($plant, $line, $status, $engine, $assembly, $number, $beginning, $qc, $examine, $assembled) = array_pad($row->toArray(), 10, null);
                if ($assembly && $number && $engine) {
                    $sql = [
                        'plant' => $dicts['plant']->where('name', trim($plant))->value('value',0),
                        'engine' => $dicts['engine_type']->where('name', trim($engine))->value('value',0),
                        'line' => $dicts['assembly_line']->where('name', trim($line))->value('value',0),
                        'assembly_id' => $dicts['assemblies']->where('name', trim($assembly))->value('value',''),
                        'status' => $dicts['assembly_status']->where('name', trim($status))->value('value',0),
                        'beginning_at' => trim($beginning) ? Carbon::parse("1900-01-01")->addDays(trim($beginning)) : null,
                        'qc_at' => trim($qc) ? Carbon::parse("1900-01-01")->addDays(trim($qc)) : null,
                        'examine_at' => trim($examine) ? Carbon::parse("1900-01-01")->addDays(trim($examine)) : null,
                        'assembled_at' => trim($assembled) ? Carbon::parse("1900-01-01")->addDays(trim($assembled)) : null,
                        'number' => trim($number)
                    ];
                    (new ProductService)->importCreate($sql);
                }
            }
        });
    }
}
