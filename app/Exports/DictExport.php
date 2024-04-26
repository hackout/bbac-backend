<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Str;

class DictExport implements FromView,WithEvents
{
    
    public function __construct(
        private array|Collection $items,
    ) {
    }

    public function view(): View
    {
        return view('exports.dict', [
            'items' => $this->items
        ]);
    }

    public function registerEvents(): array
    {
        $rowNum = 3;
        $this->items->each(function($item) use(&$rowNum){
            $rowNum += $item->items && $item->items->count() > 1 ? $item->items->count() + 1 : 1;
        });
        $range = Str::replaceArray('?', [$rowNum], 'A1:D?');
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
