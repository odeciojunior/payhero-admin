<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Services\ReportFinanceService;

class ReportsFinanceApiController extends Controller
{
    public function getFinancesResume(Request $request)
    {
        $request->validate([ 'date_range' => 'required' ]);

        $data = $request->all();

        $reportService = new ReportFinanceService();
        $resume = $reportService->getFinancesResume($data);

        return response()->json([
            'data' => $resume
        ]);
    }

    public function getFinancesCashbacks(Request $request)
    {
        $request->validate([ 'date_range' => 'required' ]);

        $data = $request->all();

        $reportService = new ReportFinanceService();
        $resume = $reportService->getFinancesCashbacks($data);

        return response()->json([
            'data' => $resume
        ]);
    }

    public function getFinancesPendings(Request $request)
    {
        $reportService = new ReportFinanceService();
        $resume = $reportService->getFinancesPendings();

        return response()->json([
            'data' => $resume
        ]);
    }

    public function getFinancesBlockeds(Request $request)
    {
        $reportService = new ReportFinanceService();
        $resume = $reportService->getFinancesBlockeds();

        return response()->json([
            'data' => $resume
        ]);
    }

    public function getFinancesDistribuitions(Request $request)
    {
        $reportService = new ReportFinanceService();
        $resume = $reportService->getFinancesDistribuitions();

        return response()->json([
            'data' => $resume
        ]);
    }
    public function getFinancesWithdrawals(Request $request)
    {
        $reportService = new ReportFinanceService();
        $resume = $reportService->getFinancesWithdrawals();

        return response()->json([
            'data' => $resume
        ]);
    }
}
