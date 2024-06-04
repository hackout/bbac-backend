<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Backend\ReportService;
use App\Services\Backend\DictService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;

/**
 * 报表控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ReportController extends Controller
{

    /**
     * 数据报表-整车服务
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function vehicle(Request $request): InertiaResponse
    {
        return Inertia::render('Report/Vehicle');
    }

    /**
     * 数据报表-产品考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function product(Request $request): InertiaResponse
    {
        return Inertia::render('Report/Product');
    }

    /**
     * 数据报表-在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function inline(Request $request): InertiaResponse
    {
        return Inertia::render('Report/Inline');
    }

    /**
     * 数据报表-整车服务
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  DictService   $dictService
     * @return InertiaResponse
     */
    public function vehicleDaily(Request $request, DictService $dictService): InertiaResponse
    {
        $date = $request->get('date', (Carbon::now())->toDateString());
        return Inertia::render('Report/Vehicle/Daily', [
            'date' => $date,
            'report' => fn() => (new ReportService)->getVehicleDaily($date),
            'motorcycle_type' => $dictService->getOptionByCode('motorcycle_type'),
            'car_series' => $dictService->getOptionByCode('car_series'),
            'sensor_point' => $dictService->getOptionByCode('sensor_point'),
            'service_shift' => $dictService->getOptionByCode('service_shift'),
            'eb_type' => $dictService->getOptionByCode('eb_type'),
            'service_factory' => $dictService->getOptionByCode('service_factory'),
        ]);
    }

    /**
     * 数据报表-产品考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function productDaily(Request $request, DictService $dictService): InertiaResponse
    {
        $date = $request->get('date', (Carbon::now())->toDateString());
        return Inertia::render('Report/Product/Daily', [
            'date' => $date,
            'report' => fn() => (new ReportService)->getProductDaily($date),
            'defect_level' => $dictService->getOptionByCode('defect_level'),
            'defect_category' => $dictService->getOptionByCode('defect_category'),
            'eb_type' => $dictService->getOptionByCode('eb_type'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'purpose' => $dictService->getOptionByCode('purpose'),
            'assembly_line' => $dictService->getOptionByCode('assembly_line'),
            'issue_status' => $dictService->getOptionByCode('issue_status')
        ]);
    }

    /**
     * 数据报表-在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function inlineDaily(Request $request): InertiaResponse
    {
        $now = Carbon::now();
        return Inertia::render('Report/Inline/Daily', [
            'date' => $now->toDateString(),
        ]);
    }

    /**
     * 数据报表-整车服务
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  DictService $dictService
     * @return InertiaResponse
     */
    public function vehicleWeekly(Request $request, DictService $dictService): InertiaResponse
    {
        $date = $request->get('date') ?? date('Y-m-d', strtotime('last monday'));
        return Inertia::render('Report/Vehicle/Weekly', [
            'date' => $date,
            'items' => fn() => (new ReportService)->getVehicleWeekly($date),
            'eb_type' => $dictService->getOptionByCode('eb_type'),
            'root_cause_type' => $dictService->getOptionByCode('root_cause_type'),
            'issue_type' => $dictService->getOptionByCode('issue_type'),
        ]);
    }

    /**
     * 数据报表-产品考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function productWeekly(Request $request): InertiaResponse
    {
        $now = Carbon::now();
        return Inertia::render('Report/Product/Weekly', [
            'date' => [$now->clone()->weekday(1)->toDateString(), $now->clone()->weekday(6)->toDateString()]
        ]);
    }

    /**
     * 数据报表-在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function inlineWeekly(Request $request): InertiaResponse
    {
        $now = Carbon::now();
        return Inertia::render('Report/Inline/Weekly', [
            'date' => [$now->clone()->weekday(1)->toDateString(), $now->clone()->weekday(6)->toDateString()]
        ]);
    }

    /**
     * 数据报表-整车服务
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  DictService $dictService
     * @return InertiaResponse
     */
    public function vehicleMonthly(Request $request, DictService $dictService): InertiaResponse
    {
        $date = $request->get('date') ?? date('Y-m');
        return Inertia::render('Report/Vehicle/Monthly', [
            'date' => $date,
            'items' => fn() => (new ReportService)->getVehicleMonthly($date),
            'eb_type' => $dictService->getOptionByCode('eb_type'),
            'root_cause_type' => $dictService->getOptionByCode('root_cause_type'),
            'issue_type' => $dictService->getOptionByCode('issue_type'),
        ]);
    }

    /**
     * 数据报表-产品考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function productMonthly(Request $request): InertiaResponse
    {
        return Inertia::render('Report/Product/Monthly', [
        ]);
    }

    /**
     * 数据报表-在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function inlineMonthly(Request $request): InertiaResponse
    {
        return Inertia::render('Report/Inline/Monthly', [
        ]);
    }


    /**
     * 数据报表-产品考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function productYearly(Request $request): InertiaResponse
    {
        return Inertia::render('Report/Product/Yearly', [
        ]);
    }

    /**
     * 数据报表-在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function inlineYearly(Request $request): InertiaResponse
    {
        return Inertia::render('Report/Inline/Yearly', [
        ]);
    }
}
