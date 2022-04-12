<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Services\ReportSaleService;

class ReportsSaleApiController extends Controller
{
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
}
