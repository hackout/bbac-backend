<?php

namespace App\Exports;

use App\Services\Private\DictService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Str;

class PlanExport implements FromView, WithEvents
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
            'plant' => $dictService->getOptionByCode('plant')
        ];
        return view('exports.plan', [
            'items' => $this->items->map(function ($item) use ($dicts) {
                $item->type = $dicts['engine_type']->where('value', $item->type)->value('name');
                $item->line = $dicts['assembly_line']->where('value', $item->line)->value('name');
                $item->plant = $dicts['plant']->where('value', $item->plant)->value('name');
                $item->casts['type'] = 'string';
                $item->casts['line'] = 'string';
                $item->casts['plant'] = 'string';
                return $item;
            })
        ]);
    }

    public function registerEvents(): array
    {
        $rowNum = 2 + $this->items->count();
        $range = Str::replaceArray('?', [$rowNum], 'A1:I?');
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
