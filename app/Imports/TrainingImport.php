<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Training;
use Illuminate\Support\Collection;
use App\Services\Private\DictService;
use App\Services\Private\UserService;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToCollection;

class TrainingImport implements ToCollection
{

    public function __construct(private int $type)
    {
    }

    public function collection(Collection $rows)
    {
        $trainingTypeStatus = (new DictService)->getOptionByCode('training_' . $this->type . '_status');
        $userNames = (new UserService)->getAllOptions();
        $trainingData = $rows->filter(function ($row, $index) {
            return $index == 2;
        })->first()->toArray();
        $trainingSql = [
            'name' => trim($trainingData[0]),
            'type' => $this->type,
            'started_at' => Carbon::parse('1900-01-01')->addDays((int) $trainingData[1] - 2),
            'period' => (int) $trainingData[2],
            'ended_at' => Carbon::parse('1900-01-01')->addDays((int) $trainingData[3] - 2),
            'status' => (new DictService)->getValueByCode('training_status', trim($trainingData[4]))
        ];
        if ($trainingSql['name']) {
            $training = Training::create($trainingSql);
            if ($training) {
                $usersData = $rows->filter(fn($row, $index) => is_numeric($row[0]) && !empty ($row[1]) && $index > 4)->values()->map(function ($row) use ($trainingTypeStatus, $userNames) {
                    $number = (string) $row[1];
                    return [
                        'name' => $userNames->where('number', $number)->value('name') ?? trim($row[2]),
                        'number' => $number,
                        'trained_at' => Carbon::parse("1900-01-01")->addDays((int) $row[3]),
                        'status' => $trainingTypeStatus->where('name', trim($row[4]))->value('value'),
                        'user_id' => $userNames->where('number', $number)->value('id') ?? null
                    ];
                })->toArray();
                if ($usersData) {
                    $training->training_users()->createMany($usersData);
                }
            }
        }
        $cacheName = (new Training)->getTable();
        $cache = Cache::get($cacheName, []);
        foreach ($cache as $key) {
            Cache::forget($key);
        }
        Cache::forget($cacheName);
    }
}
