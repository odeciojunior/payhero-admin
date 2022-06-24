<?php

namespace Modules\Reports\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Services\Reports\ReportMarketingService;
use Modules\DiscountCoupons\Transformers\DiscountCouponsResource;
use Modules\Reports\Transformers\SalesByOriginResource;

class ReportsMarketingApiController extends Controller
{
    public function getResume(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportMarketingService();
        $resume = $reportService->getResumeMarketing($data);

        return response()->json([
            'data' => $resume
        ]);
    }

    public function getSalesByState(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportMarketingService();
        $resume = $reportService->getSalesByState($data);

        return response()->json([
            'data' => $resume
        ]);
    }

    public function getMostFrequentSales(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportMarketingService();
        $mostFrequentSales = $reportService->getMostFrequentSales($data);

        return response()->json([
            'data' => $mostFrequentSales
        ]);
    }

    public function getDevices(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportMarketingService();
        $devices = $reportService->getDevices($data);

        return response()->json([
            'data' => $devices
        ]);
    }

    public function getOperationalSystems(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportMarketingService();
        $devices = $reportService->getOperationalSystems($data);

        return response()->json([
            'data' => $devices
        ]);
    }

    public function getStateDetail(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'state' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportMarketingService();
        $stateDetail = $reportService->getStateDetail($data);

        return response()->json([
            'data' => $stateDetail
        ]);
    }

    public function getResumeCoupons(Request $request)
    {
        try{
            $request->validate([
                'date_range' => 'required',
                'project_id' => 'required'
            ]);
    
            $data = $request->all();
            $reportService = new ReportMarketingService();
            $coupons = $reportService->getResumeCoupons($data);

            return response()->json([
                'data' => $coupons
            ]);
        }
        catch(Exception $e) {
            report($e);
        }
    }

    public function getResumeRegions(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportMarketingService();
        $regions = $reportService->getResumeRegions($data);

        return response()->json([
            'data' => $regions
        ]);
    }

    public function getResumeOrigins(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'origin' => 'required',
            'project_id' => 'required',
        ]);

        $data = $request->all();

        $reportService = new ReportMarketingService();
        $orders = $reportService->getResumeOrigins($data);

        $cacheName = 'origins-resume-'.json_encode($data);
        return cache()->remember($cacheName, 120, function() use ($orders) {
            return SalesByOriginResource::collection($orders->paginate(6));
        });
    }
}
