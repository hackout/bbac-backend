<?php

namespace App\Exports;

use App\Services\Private\AssemblyService;
use App\Services\Private\DictService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TorqueItemExport implements FromView, WithEvents
{

    public function __construct(
        private array|Collection $items,
    ) {
    }

    public function view(): View
    {
        $dictService = new DictService;
        $dicts = [
            'assembly_line' => $dictService->getOptionByCode('assembly_line'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'plant' => $dictService->getOptionByCode('plant'),
            'assemblies' => (new AssemblyService)->getOptions()
        ];
        return view('exports.spc', [
            'items' => $this->items->map(function ($item) use ($dicts) {
                $item['engine'] = $dicts['engine_type']->where('value', $item['engine'])->value('name');
                $item['line'] = $dicts['assembly_line']->where('value', $item['line'])->value('name');
                $item['plant'] = $dicts['plant']->where('value', $item['plant'])->value('name');
                $item['assembly_id'] = $dicts['assemblies']->where('value',$item['assembly_id'])->value('name');
                return $item;
            }),
            'months' => $this->getMonthByYear()
        ]);
    }
    
    public function getMonthByYear():array
    {
        $result = [];
        for($i=1;$i < 13;$i++)
        {
            $result[] = [
                'name' => $i,
                'date' => Carbon::today()->month($i)->day(1)
            ];
        }
        return $result;
    }

    public function registerEvents(): array
    {
        $rowNum = 3 + $this->items->count();
        $range = Str::replaceArray('?', [$rowNum], 'A1:AQ?');
        return [
            AfterSheet::class => function (AfterSheet $event) use ($range) {
                $event->sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
