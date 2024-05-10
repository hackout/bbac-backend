<?php
namespace App\Services\Backend;

use App\Imports\CommitImportSheet;
use App\Imports\CommitVehicleImport;
use App\Models\IssueVehicle;
use App\Models\User;
use App\Models\Commit;
use App\Models\CommitVehicle;
use App\Packages\CommitPlus\CommitPlus;
use App\Packages\Department\DepartmentRole;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Carbon\Carbon;

/**
 * 报表数据服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ReportService
{

    public function getVehicleDaily(string $date)
    {
        $serviceFactories = (new DictService)->getOptionByCode('service_factory');
        $engines = (new DictService)->getOptionByCode('eb_type');
        $date = Carbon::parse($date);
        $todaySql = [
            ['created_at', '>=', $date],
            ['created_at', '<', $date->clone()->addDay()]
        ];
        $list = IssueVehicle::where($todaySql)->get();
        $result = collect([
            'factories' => $serviceFactories->map(function ($item) use ($list) {
                $status = 0;
                if ($list->where('plant', $item['value'])->where('is_pre_highlight', true)->count()) {
                    $status = 1;
                }
                if ($list->where('plant', $item['value'])->where('is_pre_highlight', false)->count()) {
                    $status = 2;
                }
                return [
                    'value' => $item['value'],
                    'name' => $item['name'],
                    'status' => $status
                ];
            }),
            'engines' => $engines->map(function ($item) use ($list) {
                $status = 0;
                if ($list->where('eb_type', $item['value'])->where('is_pre_highlight', true)->count()) {
                    $status = 1;
                }
                if ($list->where('eb_type', $item['value'])->where('is_pre_highlight', false)->count()) {
                    $status = 2;
                }
                return [
                    'value' => $item['value'],
                    'name' => $item['name'],
                    'status' => $status
                ];
            }),
            'target' => $engines->map(function ($item) use ($list) {
                $status = 0;
                if ($list->where('eb_type', $item['value'])->where('is_pre_highlight', true)->count()) {
                    $status = 1;
                }
                if ($list->where('eb_type', $item['value'])->where('is_pre_highlight', false)->count()) {
                    $status = 2;
                }
                return [
                    'value' => $item['value'],
                    'name' => $item['name'],
                    'count' => $list->where('eb_type', $item['value'])->count(),
                    'status' => $status
                ];
            }),
            'ytd' => $engines->map(function ($item) use ($date) {
                $status = 0;
                $date = $date->clone()->subYear();
                $where = [
                    ['created_at', '>=', $date],
                    ['created_at', '<', $date->clone()->addDay()],
                    ['eb_type', '=', $item['value']]
                ];
                if (IssueVehicle::where($where)->where('is_pre_highlight', true)->count()) {
                    $status = 1;
                }
                if (IssueVehicle::where($where)->where('is_pre_highlight', false)->count()) {
                    $status = 2;
                }
                return [
                    'value' => $item['value'],
                    'name' => $item['name'],
                    'count' => IssueVehicle::where($where)->count(),
                    'status' => $status
                ];
            }),
            'mtd' => $engines->map(function ($item) use ($list, $date) {
                $status = 0;
                $date = $date->clone()->subMonth();
                $where = [
                    ['created_at', '>=', $date],
                    ['created_at', '<', $date->clone()->addDay()],
                    ['eb_type', '=', $item['value']]
                ];
                if (IssueVehicle::where($where)->where('is_pre_highlight', true)->count()) {
                    $status = 1;
                }
                if (IssueVehicle::where($where)->where('is_pre_highlight', false)->count()) {
                    $status = 2;
                }
                return [
                    'value' => $item['value'],
                    'name' => $item['name'],
                    'count' => IssueVehicle::where($where)->count(),
                    'status' => $status
                ];
            }),
            'preHighlight' => IssueVehicle::where($todaySql)->where('is_pre_highlight', true)->get()->map(function ($item) use ($todaySql, $date) {
                $sql = [
                    ['description', '=', $item->description],
                    ['cause', '=', $item->cause],
                    ['is_pre_highlight', '=', 1]
                ];
                return [
                    'id' => $item->id,
                    'shift' => $item->shift,
                    'eb_type' => $item->eb_type,
                    'plant' => $item->plant,
                    'product_number' => $item->product_number,
                    'description' => $item->description,
                    'initial_analysis' => $item->initial_analysis,
                    'initial_action' => $item->initial_action,
                    'created_at' => $item->created_at,
                    'root_cause' => $item->root_cause,
                    'soma' => $item->soma,
                    'is_ppm' => $item->is_ppm,
                    'is_pre_highlight' => $item->is_pre_highlight,
                    'pictures' => $item->overview_attaches + $item->master_overview_attaches + $item->detail_attaches + $item->master_detail_attaches,
                    'cause' => $item->cause,
                    'quantity' => IssueVehicle::where($sql)->where($todaySql)->count(),
                    'year_quantity' => IssueVehicle::where($sql)->where('created_at', '<=', $date)->where('created_at', '>=', $date->clone()->subYear())->count(),
                ];
            }),
            'highlight' => IssueVehicle::where($todaySql)->where('is_pre_highlight', false)->get()->map(function ($item) use ($todaySql, $date) {
                $sql = [
                    ['description', '=', $item->description],
                    ['cause', '=', $item->cause],
                    ['is_pre_highlight', '=', 0]
                ];
                return [
                    'id' => $item->id,
                    'shift' => $item->shift,
                    'eb_type' => $item->eb_type,
                    'plant' => $item->plant,
                    'product_number' => $item->product_number,
                    'description' => $item->description,
                    'initial_analysis' => $item->initial_analysis,
                    'initial_action' => $item->initial_action,
                    'created_at' => $item->created_at,
                    'root_cause' => $item->root_cause,
                    'soma' => $item->soma,
                    'is_ppm' => $item->is_ppm,
                    'is_pre_highlight' => $item->is_pre_highlight,
                    'pictures' => $item->overview_attaches + $item->master_overview_attaches + $item->detail_attaches + $item->master_detail_attaches,
                    'cause' => $item->cause,
                    'quantity' => IssueVehicle::where($sql)->where($todaySql)->count(),
                    'year_quantity' => IssueVehicle::where($sql)->where('created_at', '<=', $date)->where('created_at', '>=', $date->clone()->subYear())->count(),
                ];
            }),
            'information' => IssueVehicle::where($todaySql)->get()->map(function ($item) use ($todaySql, $date) {
                $sql = [
                    ['description', '=', $item->description],
                    ['cause', '=', $item->cause]
                ];
                return [
                    'id' => $item->id,
                    'shift' => $item->shift,
                    'eb_type' => $item->eb_type,
                    'plant' => $item->plant,
                    'product_number' => $item->product_number,
                    'description' => $item->description,
                    'initial_analysis' => $item->initial_analysis,
                    'initial_action' => $item->initial_action,
                    'created_at' => $item->created_at,
                    'root_cause' => $item->root_cause,
                    'soma' => $item->soma,
                    'is_ppm' => $item->is_ppm,
                    'is_pre_highlight' => $item->is_pre_highlight,
                    'pictures' => $item->overview_attaches + $item->master_overview_attaches + $item->detail_attaches + $item->master_detail_attaches,
                    'cause' => $item->cause,
                    'quantity' => IssueVehicle::where($sql)->where($todaySql)->count(),
                    'year_quantity' => IssueVehicle::where($sql)->where('created_at', '<=', $date)->where('created_at', '>=', $date->clone()->subYear())->count(),
                ];
            }),
            'ongoing' => IssueVehicle::where($todaySql)->where('status', IssueVehicle::STATUS_ONGOING)->get()->map(function ($item) use ($todaySql, $date) {
                $sql = [
                    ['description', '=', $item->description],
                    ['cause', '=', $item->cause],
                    ['status', '=', IssueVehicle::STATUS_ONGOING]
                ];
                return [
                    'id' => $item->id,
                    'shift' => $item->shift,
                    'eb_type' => $item->eb_type,
                    'plant' => $item->plant,
                    'product_number' => $item->product_number,
                    'description' => $item->description,
                    'initial_analysis' => $item->initial_analysis,
                    'initial_action' => $item->initial_action,
                    'created_at' => $item->created_at,
                    'root_cause' => $item->root_cause,
                    'soma' => $item->soma,
                    'is_ppm' => $item->is_ppm,
                    'is_pre_highlight' => $item->is_pre_highlight,
                    'pictures' => $item->overview_attaches + $item->master_overview_attaches + $item->detail_attaches + $item->master_detail_attaches,
                    'cause' => $item->cause,
                    'due_date' => $item->due_date,
                    'due_end' => optional($item->due_date)->diffInDays($date, true) ?? 0,
                    'quantity' => IssueVehicle::where($sql)->where($todaySql)->sum('quantity'),
                    'year_quantity' => IssueVehicle::where($sql)->where('created_at', '<=', $date)->where('created_at', '>=', $date->clone()->subYear())->sum('quantity'),
                ];
            })
        ]);
        return $result;
    }

    public function getVehicleWeekly(int $date = 0)
    {
        if (!$date) {
            $startDay = Carbon::now()->startOfWeek();
        } else {
            $startDay = Carbon::now()->startOfYear()->addWeeks($date - 1);
        }
        $endDay = $startDay->clone()->endOfWeek();
        $ebTypeList = (new DictService)->getOptionByCode('eb_type');
        $causeTypeList = (new DictService)->getOptionByCode('root_cause_type');
        return $ebTypeList->map(function ($eb_type) use ($startDay, $endDay, $causeTypeList) {
            $issues = IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<=', $endDay)->get();
            $result = [
                'name' => $eb_type['name'],
                'w' => rand(1,399),
                'y' => rand(1,399),
                'm' => rand(1,399),
                'ay4' => rand(1,399),
                'ax4' => rand(1,399),
                'bb4' => rand(1,399),
                'ar4' => rand(1,399),
                'cwList' => [
                    [
                        'name' => 'CW1',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW2',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW3',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW4',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW5',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW6',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW7',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW8',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW9',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW10',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW11',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW12',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW13',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW14',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW15',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW16',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ]
                ],
                'causeTypeList' => $causeTypeList->map(function ($item) use ($issues) {
                    return [
                        'value' => $item['value'],
                        'name' => $item['name'],
                        'count' => rand(1,399)// $issues->where('cause_type', $item['value'])->count()
                    ];
                })->filter(fn($n) => $n['count'])->values(),
                'eight' => IssueVehicle::where('created_at', '>=', $startDay)
                    ->where('created_at', '<=', $endDay)
                    ->orderBy('created_at', 'DESC')
                    ->skip(0)->take(8)->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'qty' => $item->quantity,
                            'description' => $item->description,
                            'issue_type' => $item->type,
                            'cause_type' => $item->cause_type
                        ];
                    }),
            ];
            return $result;
        });
    }
    public function getVehicleMonthly(string $date)
    {
        $startDay = Carbon::parse($date.'-01');
        $endDay = $startDay->clone()->endOfWeek();
        $ebTypeList = (new DictService)->getOptionByCode('eb_type');
        $causeTypeList = (new DictService)->getOptionByCode('root_cause_type');
        return $ebTypeList->map(function ($eb_type) use ($startDay, $endDay, $causeTypeList) {
            $issues = IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<=', $endDay)->get();
            $result = [
                'name' => $eb_type['name'],
                'w' => rand(1,399),
                'y' => rand(1,399),
                'm' => rand(1,399),
                'ay4' => rand(1,399),
                'ax4' => rand(1,399),
                'bb4' => rand(1,399),
                'ar4' => rand(1,399),
                'cwList' => [
                    [
                        'name' => 'CW1',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW2',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW3',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW4',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW5',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW6',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW7',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW8',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW9',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW10',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW11',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW12',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW13',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW14',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW15',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ],
                    [
                        'name' => 'CW16',
                        'count' => rand(1,399),
                        'sum' => rand(1,399)
                    ]
                ],
                'causeTypeList' => $causeTypeList->map(function ($item) use ($issues) {
                    return [
                        'value' => $item['value'],
                        'name' => $item['name'],
                        'count' => rand(1,399)// $issues->where('cause_type', $item['value'])->count()
                    ];
                })->filter(fn($n) => $n['count'])->values(),
                'eight' => IssueVehicle::where('created_at', '>=', $startDay)
                    ->where('created_at', '<=', $endDay)
                    ->orderBy('created_at', 'DESC')
                    ->skip(0)->take(8)->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'qty' => $item->quantity,
                            'description' => $item->description,
                            'issue_type' => $item->type,
                            'cause_type' => $item->cause_type
                        ];
                    }),
            ];
            return $result;
        });
    }
}