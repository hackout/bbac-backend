<?php

namespace App\Exports;

use App\Packages\Excel\ExcelPlus;
use App\Services\Private\DictService;
use App\Services\Private\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Str;

class TrainingExport implements FromView, WithEvents
{
    private array $users = [];
    private ?Collection $status;
    private ?Collection $item_status;

    private int $type = 0;

    public function __construct(
        private array|Collection $items,
    ) {
        $first = $this->items->first();
        $this->type = $first['type'];
        $userIdList = $first['users'] ? array_keys($first['users']) : [];
        if ($userIdList) {
            $this->users = (new UserService)->getOptionByIdList($userIdList);
        }
        $this->status = (new DictService)->getOptionByCode('training_status');
        $this->item_status = (new DictService)->getOptionByCode('training_'.$this->type.'_status');
    }

    public function view(): View
    {
        return view('exports.training', [
            'items' => $this->items,
            'users' => $this->users,
            'type' => $this->type,
            'status' => $this->status,
            'item_status' => $this->item_status
        ]);
    }

    public function registerEvents(): array
    {
        $rowNum = 2 + $this->items->count();
        $totalUser = count($this->users);
        $totalCells = 6 + ($totalUser ? $totalUser + 1 : 0);
        $range = Str::replaceArray('?', [ExcelPlus::numberToLetter($totalCells), $rowNum], 'A1:??');
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
