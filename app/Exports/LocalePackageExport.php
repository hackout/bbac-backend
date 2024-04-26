<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Str;

class LocalePackageExport implements FromView,WithEvents
{
    
    public function __construct(
        private array|Collection $items,
    ) {
    }

    public function view(): View
    {
        return view('exports.locale_package', [
            'items' => $this->items
        ]);
    }

    public function registerEvents(): array
    {
        $rowNum = 2 + count($this->items);
        $range = Str::replaceArray('?', [$rowNum], 'A1:C?');
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
