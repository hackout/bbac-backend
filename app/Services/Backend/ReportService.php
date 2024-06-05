<?php
namespace App\Services\Backend;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\Product;
use App\Models\VehicleTarget;
use App\Models\VehicleOutbound;
use App\Models\IssueVehicle;
use App\Models\IssueProduct;
use App\Models\ExamineProduct;
use Illuminate\Database\Eloquent\Collection;

/**
 * 报表数据服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ReportService
{

    /**
     * 整车日报
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $date
     * @return array|Collection
     */
    public function getVehicleDaily(string $date):array|Collection
    {
        $date = Carbon::parse($date);
        $serviceFactories = (new DictService)->getOptionByCode('service_factory', true);
        $engines = (new DictService)->getOptionByCode('eb_type', true);
        $todaySql = [
            ['created_at', '>=', $date],
            ['created_at', '<', $date->clone()->addDay()]
        ];
        $list = IssueVehicle::where($todaySql)->get();
        $targets = (new VehicleTargetService())->setQuery(['yearly' => $date->year])->getAll();
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
                    'thumbnail' => $item['thumbnail'],
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
                    'thumbnail' => $item['thumbnail'],
                    'status' => $status
                ];
            }),
            'target' => $engines->map(function ($item) use ($targets) {
                $status = 0;
                return [
                    'value' => $item['value'],
                    'name' => $item['name'],
                    'count' => optional($targets->where('eb_type', $item['value'])->first())->target ?? 0,
                    'status' => $status
                ];
            }),
            'ytd' => $engines->map(function ($item) use ($date) {
                $startDate = $date->clone()->firstOfYear();
                $endDate = $date->clone()->addDay();
                $where = [
                    ['daily', '>=', $startDate],
                    ['daily', '<', $endDate]
                ];
                $outboundSum = VehicleOutbound::where($where)->sum('outbound');
                $status = 0;
                $where = [
                    ['created_at', '>=', $startDate],
                    ['created_at', '<', $endDate],
                    ['eb_type', '=', $item['value']]
                ];
                $infoCount = IssueVehicle::where($where)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count();
                $preCount = IssueVehicle::where($where)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count();
                $highCount = IssueVehicle::where($where)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count();
                if ($preCount > $highCount) {
                    $status = 1;
                }
                if ($highCount > $preCount) {
                    $status = 2;
                }
                $count = 0;
                if ($outboundSum > 0 && ($infoCount + $preCount + $highCount) > 0) {
                    $count = ($infoCount + $preCount + $highCount) / $outboundSum * 1000000;
                }
                return [
                    'value' => $item['value'],
                    'name' => $item['name'],
                    'count' => $count,
                    'status' => $status
                ];
            }),
            'mtd' => $engines->map(function ($item) use ($date) {
                $startDate = $date->clone()->firstOfMonth();
                $endDate = $date->clone()->addDay();
                $where = [
                    ['daily', '>=', $startDate],
                    ['daily', '<', $endDate]
                ];
                $outboundSum = VehicleOutbound::where($where)->sum('outbound');
                $status = 0;
                $where = [
                    ['created_at', '>=', $startDate],
                    ['created_at', '<', $endDate],
                    ['eb_type', '=', $item['value']]
                ];
                $infoCount = IssueVehicle::where($where)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count();
                $preCount = IssueVehicle::where($where)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count();
                $highCount = IssueVehicle::where($where)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count();
                if ($preCount > $highCount) {
                    $status = 1;
                }
                if ($highCount > $preCount) {
                    $status = 2;
                }
                $count = 0;
                if ($outboundSum > 0 && ($infoCount + $preCount + $highCount) > 0) {
                    $count = ($infoCount + $preCount + $highCount) / $outboundSum * 1000000;
                }
                return [
                    'value' => $item['value'],
                    'name' => $item['name'],
                    'count' => $count,
                    'status' => $status
                ];
            }),
            'preHighlight' => IssueVehicle::where($todaySql)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->get()->map(function ($item) use ($todaySql, $date) {
                $sql = [
                    ['description', '=', $item->description],
                    ['cause', '=', $item->cause],
                    ['issue_type', '=', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT]
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
            'highlight' => IssueVehicle::where($todaySql)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->get()->map(function ($item) use ($todaySql, $date) {
                $sql = [
                    ['description', '=', $item->description],
                    ['cause', '=', $item->cause],
                    ['issue_type', '=', IssueVehicle::ISSUE_TYPE_HIGHLIGHT]
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
            'information' => IssueVehicle::where($todaySql)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->get()->map(function ($item) use ($todaySql, $date) {
                $sql = [
                    ['description', '=', $item->description],
                    ['cause', '=', $item->cause],
                    ['issue_type', '=', IssueVehicle::ISSUE_TYPE_INFORMATION]
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

    /**
     * 整车周报
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $date
     * @return array|Collection
     */
    public function getVehicleWeekly(string $date):array|Collection
    {
        $startDay = Carbon::parse($date);
        $endDay = $startDay->clone()->endOfWeek();
        $ebTypeList = (new DictService)->getOptionByCode('eb_type', true);
        $causeTypeList = (new DictService)->getOptionByCode('root_cause_type');
        return $ebTypeList->map(function ($eb_type) use ($startDay, $endDay, $causeTypeList) {
            $issues = IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<=', $endDay)->get();
            $wList = [
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $yList = [
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $mList = [
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $cwList = [];
            $_start = $startDay->clone()->startOfYear();
            for ($i = 0; $i < $startDay->isoWeeksInYear(); $i++) {
                $dates = [
                    !$i ? $_start : $_start->clone()->addDays(7 * $i)->startOfWeek()
                ];
                $dates[] = $dates[0]->clone()->addDays(8);
                $cwList[] = [
                    'name' => 'CW' . ($i + 1),
                    'count' => IssueVehicle::where('created_at', '>=', $dates[0])->where('created_at', '<', $dates[1])->where('is_ppm', true)->count(),
                    'sum' => IssueVehicle::where('created_at', '>=', $dates[0])->where('created_at', '<', $dates[1])->where('is_ppm', true)->sum('quantity'),
                ];
            }
            $result = [
                'name' => $eb_type['name'],
                'thumbnail' => $eb_type['thumbnail'],
                'current' => $startDay->weekOfYear,
                'w' => $wList[0] > $wList[1] ? 2 : ($wList[1] > $wList[0] ? 1 : 0),
                'y' => $yList[0] > $yList[1] ? 2 : ($yList[1] > $yList[0] ? 1 : 0),
                'm' => $mList[0] > $mList[1] ? 2 : ($mList[1] > $mList[0] ? 1 : 0),
                'ay4' => VehicleOutbound::where([['daily', '>=', $startDay->clone()->startOfYear()], ['daily', '<', $endDay], ['eb_type', '=', $eb_type['value']]])->sum('outbound'),
                'ax4' => VehicleOutbound::where([['daily', '>=', $startDay], ['daily', '<', $endDay], ['eb_type', '=', $eb_type['value']]])->sum('outbound'),
                'bb4' => VehicleTarget::where(['yearly' => $startDay->year, 'eb_type' => $eb_type['value']])->value('target') ?? 0,
                'ar4' => $issues->where('is_ppm', true)->count(),
                'cwList' => $cwList,
                'causeTypeList' => $causeTypeList->map(function ($item) use ($issues) {
                    return [
                        'value' => $item['value'],
                        'name' => $item['name'],
                        'count' => $issues->where('cause_type', $item['value'])->count()
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

    /**
     * 整车月报
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $date
     * @return array|Collection
     */
    public function getVehicleMonthly(string $date):array|Collection
    {
        $startDay = Carbon::parse($date . '-01');
        $endDay = $startDay->clone()->endOfMonth();
        $ebTypeList = (new DictService)->getOptionByCode('eb_type', true);
        $causeTypeList = (new DictService)->getOptionByCode('root_cause_type');
        return $ebTypeList->map(function ($eb_type) use ($startDay, $endDay, $causeTypeList) {
            $issues = IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<=', $endDay)->get();
            $wList = [
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $yList = [
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $mList = [
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $cwList = [];
            $_start = $startDay->clone()->startOfYear();
            for ($i = 0; $i < $startDay->isoWeeksInYear(); $i++) {
                $dates = [
                    !$i ? $_start : $_start->clone()->addDays(7 * $i)->startOfWeek()
                ];
                $dates[] = $dates[0]->clone()->addDays(8);
                $cwList[] = [
                    'name' => 'CW' . ($i + 1),
                    'count' => IssueVehicle::where('created_at', '>=', $dates[0])->where('created_at', '<', $dates[1])->where('is_ppm', true)->count(),
                    'sum' => IssueVehicle::where('created_at', '>=', $dates[0])->where('created_at', '<', $dates[1])->where('is_ppm', true)->sum('quantity'),
                ];
            }
            $result = [
                'name' => $eb_type['name'],
                'thumbnail' => $eb_type['thumbnail'],
                'current' => $startDay->weekOfYear . '-' . $startDay->clone()->addMonth()->subDay()->weekOfYear,
                'w' => $wList[0] > $wList[1] ? 2 : ($wList[1] > $wList[0] ? 1 : 0),
                'y' => $yList[0] > $yList[1] ? 2 : ($yList[1] > $yList[0] ? 1 : 0),
                'm' => $mList[0] > $mList[1] ? 2 : ($mList[1] > $mList[0] ? 1 : 0),
                'ay4' => VehicleOutbound::where([['daily', '>=', $startDay->clone()->startOfYear()], ['daily', '<', $endDay], ['eb_type', '=', $eb_type['value']]])->sum('outbound'),
                'ax4' => VehicleOutbound::where([['daily', '>=', $startDay], ['daily', '<', $endDay], ['eb_type', '=', $eb_type['value']]])->sum('outbound'),
                'bb4' => VehicleTarget::where(['yearly' => $startDay->year, 'eb_type' => $eb_type['value']])->value('target') ?? 0,
                'ar4' => $issues->where('is_ppm', true)->count(),
                'cwList' => $cwList,
                'causeTypeList' => $causeTypeList->map(function ($item) use ($issues) {
                    return [
                        'value' => $item['value'],
                        'name' => $item['name'],
                        'count' => $issues->where('cause_type', $item['value'])->count()
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


    /**
     * 产品日报
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $date
     * @return array|Collection
     */
    public function getProductDaily(string $date):array|Collection
    {
        $engines = (new DictService)->getOptionByCode('eb_type');
        $date = Carbon::parse($date);
        $month = $date->clone()->subMonth();
        $todaySql = [
            ['created_at', '>=', $date],
            ['created_at', '<', $date->clone()->addDay()]
        ];
        $ebNumbers = Task::where($todaySql)->where('type', Task::TYPE_PRODUCT)->select('eb_number')->skip(0)->take(5)->get()->pluck('eb_number')->toArray();
        $overviews = $trends = [];
        foreach ($ebNumbers as $eb_number) {
            $product = Product::where('number', $eb_number)->first();
            $audit = Task::where('eb_number', $eb_number)->where('original_examine->type', ExamineProduct::TYPE_OVERHAUL)->orderBy('created_at', 'DESC')->first();
            $assembly = Task::where('eb_number', $eb_number)->where('original_examine->type', ExamineProduct::TYPE_ASSEMBLING)->orderBy('created_at', 'DESC')->first();
            $overviews[] = [
                'number' => $product->number,
                'audit_progress' => $audit && $audit->extra ? $audit->extra['progress'] : 0,
                'assembly_progress' => $assembly && $assembly->extra ? $assembly->extra['progress'] : 0,
                'status' => IssueProduct::where('product_id', $product->id)->value('status') ?? 0,
                'defect_level' => IssueProduct::where('product_id', $product->id)->value('defect_level') ?? 0,
                'description' => IssueProduct::where('product_id', $product->id)->value('defect_category') ?? 0,
                'is_ok' => IssueProduct::where('product_id', $product->id)->value('is_ok') ?? false,
            ];
        }
        $sql = [
            ['created_at', '>=', $month],
            ['created_at', '<', $date->clone()->addDay()],
            ['type', '=', Task::TYPE_PRODUCT]
        ];
        $trendList = Task::where($sql)->select('engine', 'extra', 'created_at', 'assembly_id')->get();
        $engines->each(function ($engine) use (&$trends, $trendList, $month, $date) {
            $array = [];
            $score = 0;
            for ($i = 0; $i < intval($date->diffInDays($month)); $i++) {
                $_score = 0;
                $today = $i ? $month->clone()->addDays($i) : $month;
                $trendList->filter(function ($item) use ($today, $engine) {
                    return !intval($item->created_at->diffInDays($today)) && $item->engine == $engine['value'];
                })->values()->each(function ($item) use (&$_score) {
                    if ($item->extra && array_key_exists('score', $item->extra)) {
                        $_score += $item->extra['score'];
                    }
                });
                $array[] = $_score;
                $score += $_score;
            }
            if ($array) {
                $trends[] = [
                    'engine' => $engine['name'],
                    'trend' => $array,
                    'score' => $score
                ];
            }
        });
        $assemblyList = $trendList->pluck('assembly_id')->toArray();
        $issueOverviews = [];
        $issues = IssueProduct::where($todaySql)->whereIn('assembly_id', $assemblyList)->get()->filter(fn($item) => !empty (optional($item->assembly)->number))->values()->map(function (IssueProduct $item) use (&$issueOverviews) {
            $assembly = $item->assembly->number;
            if ($item->task->original_examine['type'] != ExamineProduct::TYPE_DYNAMIC) {
                if (!array_key_exists($assembly, $issueOverviews)) {
                    $issueOverviews[$assembly] = [
                        'number' => $item->assembly->number,
                        'assembly' => [[0, 0, 0], [0, 0, 0], [0, 0, 0]],
                        'audit' => [[0, 0, 0], [0, 0, 0], [0, 0, 0]],
                        'status' => 0,
                        'categories' => []
                    ];
                }
                $type = $item->task->original_examine['type'] == ExamineProduct::TYPE_OVERHAUL ? 'audit' : 'assembly';
                if (in_array($item->assembly->status, [2, 3, 4])) {
                    $issueOverviews[$assembly][$type][$item->assembly->status - 2]++;
                }
                $issueOverviews[$assembly]['status'] = $item->status;
                $issueOverviews[$assembly]['categories'][] = [
                    'defect_description' => $item->defect_description,
                    'defect_level' => $item->defect_level,
                    'thumbnail' => $item->defect_attaches ? $item->defect_attaches[0] : null
                ];
            }
            return [
                'id' => $item->id,
                'assembly' => $assembly,
                'auditor' => optional(optional($item->user)->profile)->name ?? optional($item->user)->number,
                'line' => $item->line,
                'engine' => $item->engine,
                'number' => optional($item->task)->eb_number,
                'purpose' => optional(optional($item->task)->extra)['purpose'],
                'defect_description' => $item->defect_description,
                'defect_level' => $item->defect_level,
                'defect_part' => $item->defect_part,
                'defect_position' => $item->defect_position,
                'defect_cause' => $item->defect_cause,
                'soma' => $item->soma,
                'lama' => $item->lama,
                'note' => $item->note,
                'eight_disciplines' => $item->eight_disciplines,
                'created_at' => $item->created_at,
                'ira' => $item->ira,
                'next' => $item->lama,
                'thumbnails' => $item->defect_attaches
            ];
        });


        $result = collect([
            'overviews' => $overviews,
            'trends' => $trends,
            'items' => $issueOverviews,
            'issues' => $issues
        ]);
        return $result;
    }

    /**
     * 产品周报
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $date
     * @return array|Collection
     */
    public function getProductWeekly(string $date):array|Collection
    {
        $startDay = Carbon::parse($date);
        $endDay = $startDay->clone()->endOfWeek();
        $ebTypeList = (new DictService)->getOptionByCode('eb_type', true);
        $causeTypeList = (new DictService)->getOptionByCode('root_cause_type');
        return $ebTypeList->map(function ($eb_type) use ($startDay, $endDay, $causeTypeList) {
            $issues = IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<=', $endDay)->get();
            $wList = [
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $yList = [
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $mList = [
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $cwList = [];
            $_start = $startDay->clone()->startOfYear();
            for ($i = 0; $i < $startDay->isoWeeksInYear(); $i++) {
                $dates = [
                    !$i ? $_start : $_start->clone()->addDays(7 * $i)->startOfWeek()
                ];
                $dates[] = $dates[0]->clone()->addDays(8);
                $cwList[] = [
                    'name' => 'CW' . ($i + 1),
                    'count' => IssueVehicle::where('created_at', '>=', $dates[0])->where('created_at', '<', $dates[1])->where('is_ppm', true)->count(),
                    'sum' => IssueVehicle::where('created_at', '>=', $dates[0])->where('created_at', '<', $dates[1])->where('is_ppm', true)->sum('quantity'),
                ];
            }
            $result = [
                'name' => $eb_type['name'],
                'thumbnail' => $eb_type['thumbnail'],
                'current' => $startDay->weekOfYear,
                'w' => $wList[0] > $wList[1] ? 2 : ($wList[1] > $wList[0] ? 1 : 0),
                'y' => $yList[0] > $yList[1] ? 2 : ($yList[1] > $yList[0] ? 1 : 0),
                'm' => $mList[0] > $mList[1] ? 2 : ($mList[1] > $mList[0] ? 1 : 0),
                'ay4' => VehicleOutbound::where([['daily', '>=', $startDay->clone()->startOfYear()], ['daily', '<', $endDay], ['eb_type', '=', $eb_type['value']]])->sum('outbound'),
                'ax4' => VehicleOutbound::where([['daily', '>=', $startDay], ['daily', '<', $endDay], ['eb_type', '=', $eb_type['value']]])->sum('outbound'),
                'bb4' => VehicleTarget::where(['yearly' => $startDay->year, 'eb_type' => $eb_type['value']])->value('target') ?? 0,
                'ar4' => $issues->where('is_ppm', true)->count(),
                'cwList' => $cwList,
                'causeTypeList' => $causeTypeList->map(function ($item) use ($issues) {
                    return [
                        'value' => $item['value'],
                        'name' => $item['name'],
                        'count' => $issues->where('cause_type', $item['value'])->count()
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
    

    /**
     * 产品月报
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $date
     * @return array|Collection
     */
    public function getProductMonthly(string $date):array|Collection
    {
        $startDay = Carbon::parse($date);
        $endDay = $startDay->clone()->endOfWeek();
        $ebTypeList = (new DictService)->getOptionByCode('eb_type', true);
        $causeTypeList = (new DictService)->getOptionByCode('root_cause_type');
        return $ebTypeList->map(function ($eb_type) use ($startDay, $endDay, $causeTypeList) {
            $issues = IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<=', $endDay)->get();
            $wList = [
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $yList = [
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $mList = [
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $cwList = [];
            $_start = $startDay->clone()->startOfYear();
            for ($i = 0; $i < $startDay->isoWeeksInYear(); $i++) {
                $dates = [
                    !$i ? $_start : $_start->clone()->addDays(7 * $i)->startOfWeek()
                ];
                $dates[] = $dates[0]->clone()->addDays(8);
                $cwList[] = [
                    'name' => 'CW' . ($i + 1),
                    'count' => IssueVehicle::where('created_at', '>=', $dates[0])->where('created_at', '<', $dates[1])->where('is_ppm', true)->count(),
                    'sum' => IssueVehicle::where('created_at', '>=', $dates[0])->where('created_at', '<', $dates[1])->where('is_ppm', true)->sum('quantity'),
                ];
            }
            $result = [
                'name' => $eb_type['name'],
                'thumbnail' => $eb_type['thumbnail'],
                'current' => $startDay->weekOfYear,
                'w' => $wList[0] > $wList[1] ? 2 : ($wList[1] > $wList[0] ? 1 : 0),
                'y' => $yList[0] > $yList[1] ? 2 : ($yList[1] > $yList[0] ? 1 : 0),
                'm' => $mList[0] > $mList[1] ? 2 : ($mList[1] > $mList[0] ? 1 : 0),
                'ay4' => VehicleOutbound::where([['daily', '>=', $startDay->clone()->startOfYear()], ['daily', '<', $endDay], ['eb_type', '=', $eb_type['value']]])->sum('outbound'),
                'ax4' => VehicleOutbound::where([['daily', '>=', $startDay], ['daily', '<', $endDay], ['eb_type', '=', $eb_type['value']]])->sum('outbound'),
                'bb4' => VehicleTarget::where(['yearly' => $startDay->year, 'eb_type' => $eb_type['value']])->value('target') ?? 0,
                'ar4' => $issues->where('is_ppm', true)->count(),
                'cwList' => $cwList,
                'causeTypeList' => $causeTypeList->map(function ($item) use ($issues) {
                    return [
                        'value' => $item['value'],
                        'name' => $item['name'],
                        'count' => $issues->where('cause_type', $item['value'])->count()
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
    

    /**
     * 产品年报
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $date
     * @return array|Collection
     */
    public function getProductYearly(string $date):array|Collection
    {
        $startDay = Carbon::parse($date);
        $endDay = $startDay->clone()->endOfWeek();
        $ebTypeList = (new DictService)->getOptionByCode('eb_type', true);
        $causeTypeList = (new DictService)->getOptionByCode('root_cause_type');
        return $ebTypeList->map(function ($eb_type) use ($startDay, $endDay, $causeTypeList) {
            $issues = IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<=', $endDay)->get();
            $wList = [
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $yList = [
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $mList = [
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $cwList = [];
            $_start = $startDay->clone()->startOfYear();
            for ($i = 0; $i < $startDay->isoWeeksInYear(); $i++) {
                $dates = [
                    !$i ? $_start : $_start->clone()->addDays(7 * $i)->startOfWeek()
                ];
                $dates[] = $dates[0]->clone()->addDays(8);
                $cwList[] = [
                    'name' => 'CW' . ($i + 1),
                    'count' => IssueVehicle::where('created_at', '>=', $dates[0])->where('created_at', '<', $dates[1])->where('is_ppm', true)->count(),
                    'sum' => IssueVehicle::where('created_at', '>=', $dates[0])->where('created_at', '<', $dates[1])->where('is_ppm', true)->sum('quantity'),
                ];
            }
            $result = [
                'name' => $eb_type['name'],
                'thumbnail' => $eb_type['thumbnail'],
                'current' => $startDay->weekOfYear,
                'w' => $wList[0] > $wList[1] ? 2 : ($wList[1] > $wList[0] ? 1 : 0),
                'y' => $yList[0] > $yList[1] ? 2 : ($yList[1] > $yList[0] ? 1 : 0),
                'm' => $mList[0] > $mList[1] ? 2 : ($mList[1] > $mList[0] ? 1 : 0),
                'ay4' => VehicleOutbound::where([['daily', '>=', $startDay->clone()->startOfYear()], ['daily', '<', $endDay], ['eb_type', '=', $eb_type['value']]])->sum('outbound'),
                'ax4' => VehicleOutbound::where([['daily', '>=', $startDay], ['daily', '<', $endDay], ['eb_type', '=', $eb_type['value']]])->sum('outbound'),
                'bb4' => VehicleTarget::where(['yearly' => $startDay->year, 'eb_type' => $eb_type['value']])->value('target') ?? 0,
                'ar4' => $issues->where('is_ppm', true)->count(),
                'cwList' => $cwList,
                'causeTypeList' => $causeTypeList->map(function ($item) use ($issues) {
                    return [
                        'value' => $item['value'],
                        'name' => $item['name'],
                        'count' => $issues->where('cause_type', $item['value'])->count()
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

    

    /**
     * 在线月报
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $date
     * @return array|Collection
     */
    public function getInlineMonthly(string $date):array|Collection
    {
        $startDay = Carbon::parse($date);
        $endDay = $startDay->clone()->endOfWeek();
        $ebTypeList = (new DictService)->getOptionByCode('eb_type', true);
        $causeTypeList = (new DictService)->getOptionByCode('root_cause_type');
        return $ebTypeList->map(function ($eb_type) use ($startDay, $endDay, $causeTypeList) {
            $issues = IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<=', $endDay)->get();
            $wList = [
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $yList = [
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $mList = [
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $cwList = [];
            $_start = $startDay->clone()->startOfYear();
            for ($i = 0; $i < $startDay->isoWeeksInYear(); $i++) {
                $dates = [
                    !$i ? $_start : $_start->clone()->addDays(7 * $i)->startOfWeek()
                ];
                $dates[] = $dates[0]->clone()->addDays(8);
                $cwList[] = [
                    'name' => 'CW' . ($i + 1),
                    'count' => IssueVehicle::where('created_at', '>=', $dates[0])->where('created_at', '<', $dates[1])->where('is_ppm', true)->count(),
                    'sum' => IssueVehicle::where('created_at', '>=', $dates[0])->where('created_at', '<', $dates[1])->where('is_ppm', true)->sum('quantity'),
                ];
            }
            $result = [
                'name' => $eb_type['name'],
                'thumbnail' => $eb_type['thumbnail'],
                'current' => $startDay->weekOfYear,
                'w' => $wList[0] > $wList[1] ? 2 : ($wList[1] > $wList[0] ? 1 : 0),
                'y' => $yList[0] > $yList[1] ? 2 : ($yList[1] > $yList[0] ? 1 : 0),
                'm' => $mList[0] > $mList[1] ? 2 : ($mList[1] > $mList[0] ? 1 : 0),
                'ay4' => VehicleOutbound::where([['daily', '>=', $startDay->clone()->startOfYear()], ['daily', '<', $endDay], ['eb_type', '=', $eb_type['value']]])->sum('outbound'),
                'ax4' => VehicleOutbound::where([['daily', '>=', $startDay], ['daily', '<', $endDay], ['eb_type', '=', $eb_type['value']]])->sum('outbound'),
                'bb4' => VehicleTarget::where(['yearly' => $startDay->year, 'eb_type' => $eb_type['value']])->value('target') ?? 0,
                'ar4' => $issues->where('is_ppm', true)->count(),
                'cwList' => $cwList,
                'causeTypeList' => $causeTypeList->map(function ($item) use ($issues) {
                    return [
                        'value' => $item['value'],
                        'name' => $item['name'],
                        'count' => $issues->where('cause_type', $item['value'])->count()
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
    

    /**
     * 在线年报
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $date
     * @return array|Collection
     */
    public function getInlineYearly(string $date):array|Collection
    {
        $startDay = Carbon::parse($date);
        $endDay = $startDay->clone()->endOfWeek();
        $ebTypeList = (new DictService)->getOptionByCode('eb_type', true);
        $causeTypeList = (new DictService)->getOptionByCode('root_cause_type');
        return $ebTypeList->map(function ($eb_type) use ($startDay, $endDay, $causeTypeList) {
            $issues = IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<=', $endDay)->get();
            $wList = [
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay)->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $yList = [
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfYear())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $mList = [
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_PRE_HIGHLIGHT)->count(),
                IssueVehicle::where('created_at', '>=', $startDay->clone()->firstOfMonth())->where('created_at', '<', $endDay)->where('issue_type', IssueVehicle::ISSUE_TYPE_INFORMATION)->count()
            ];
            $cwList = [];
            $_start = $startDay->clone()->startOfYear();
            for ($i = 0; $i < $startDay->isoWeeksInYear(); $i++) {
                $dates = [
                    !$i ? $_start : $_start->clone()->addDays(7 * $i)->startOfWeek()
                ];
                $dates[] = $dates[0]->clone()->addDays(8);
                $cwList[] = [
                    'name' => 'CW' . ($i + 1),
                    'count' => IssueVehicle::where('created_at', '>=', $dates[0])->where('created_at', '<', $dates[1])->where('is_ppm', true)->count(),
                    'sum' => IssueVehicle::where('created_at', '>=', $dates[0])->where('created_at', '<', $dates[1])->where('is_ppm', true)->sum('quantity'),
                ];
            }
            $result = [
                'name' => $eb_type['name'],
                'thumbnail' => $eb_type['thumbnail'],
                'current' => $startDay->weekOfYear,
                'w' => $wList[0] > $wList[1] ? 2 : ($wList[1] > $wList[0] ? 1 : 0),
                'y' => $yList[0] > $yList[1] ? 2 : ($yList[1] > $yList[0] ? 1 : 0),
                'm' => $mList[0] > $mList[1] ? 2 : ($mList[1] > $mList[0] ? 1 : 0),
                'ay4' => VehicleOutbound::where([['daily', '>=', $startDay->clone()->startOfYear()], ['daily', '<', $endDay], ['eb_type', '=', $eb_type['value']]])->sum('outbound'),
                'ax4' => VehicleOutbound::where([['daily', '>=', $startDay], ['daily', '<', $endDay], ['eb_type', '=', $eb_type['value']]])->sum('outbound'),
                'bb4' => VehicleTarget::where(['yearly' => $startDay->year, 'eb_type' => $eb_type['value']])->value('target') ?? 0,
                'ar4' => $issues->where('is_ppm', true)->count(),
                'cwList' => $cwList,
                'causeTypeList' => $causeTypeList->map(function ($item) use ($issues) {
                    return [
                        'value' => $item['value'],
                        'name' => $item['name'],
                        'count' => $issues->where('cause_type', $item['value'])->count()
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