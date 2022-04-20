<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Services\Reports\ReportSaleService;

class ReportsSaleApiController extends Controller
{
    public function getResumeSales(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportSaleService();
        $sales = $reportService->getResumeSales($data);

        return response()->json(['data' => $sales]);
    }

    public function getResumeTypePayments(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportSaleService();
        $typePayments = $reportService->getResumeTypePayments($data);

        return response()->json([
            'data' => $typePayments
        ]);
    }

    public function getResumeProducts(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportSaleService();
        $products = $reportService->getResumeProducts($data);

        return response()->json([
            'data' => $products
        ]);
    }

    public function getSalesResume(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportSaleService();
        $resume = $reportService->getSalesResume($data);

        return response()->json([
            'data' => $resume
        ]);
    }

    public function getSalesDistribuitions(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportSaleService();
        $resume = $reportService->getSalesDistribuitions($data);

        return response()->json([
            'data' => $resume
        ]);
    }

    public function getAbandonedCarts(Request $request)
    {
        $request->validate([
            'date_range' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportSaleService();
        $products = $reportService->getAbandonedCarts($data);

        return response()->json([
            'data' => $products
        ]);
    }

    public function getOrderBump(Request $request)
    {
        $request->validate([
            'date_range' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportSaleService();
        $products = $reportService->getOrderBump($data);

        return response()->json([
            'data' => $products
        ]);
    }

    public function getUpsell(Request $request)
    {
        $request->validate([
            'date_range' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportSaleService();
        $products = $reportService->getUpsell($data);

        return response()->json([
            'data' => $products
        ]);
    }

    public function getConversion(Request $request)
    {
        $request->validate([
            'date_range' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportSaleService();
        $products = $reportService->getConversion($data);

        return response()->json([
            'data' => $products
        ]);
    }

    public function getRecurrence(Request $request)
    {
        $request->validate([
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportSaleService();
        $products = $reportService->getRecurrence($data);

        return response()->json([
            'data' => $products
        ]);
    }
}
