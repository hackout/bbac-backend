<?php

namespace App\Exports;

use App\Services\Private\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Str;

class UserExport implements FromView,WithEvents
{
    
    public function __construct(
        private array|Collection $items,
    ) {
    }

    public function view(): View
    {
        $userIdList = $this->items->pluck('id')->toArray();
        $items = (new UserService)->getUserForExport($userIdList);

        return view('exports.user', [
            'items' => $items
        ]);
    }

    public function registerEvents(): array
    {
        $rowNum = 2 + count($this->items);
        $range = Str::replaceArray('?', [$rowNum], 'A1:Y?');
        return [
            AfterSheet::class => function (AfterSheet $event) use ($range) {
                $event->sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'medium',
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
