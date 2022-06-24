<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Services\Reports\ReportFinanceService;

class ReportsFinanceApiController extends Controller
{
    public function getResumeCommissions(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportFinanceService();
        $comission = $reportService->getResumeCommissions($data);

        return response()->json(['data' => $comission]);
    }

    public function getResumePendings(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportFinanceService();
        $pending = $reportService->getResumePendings($data);

        return response()->json(['data' => $pending]);
    }

    public function getResumeCashbacks(Request $request)
    {
        $request->validate([
            'date_range' => 'required',
            'project_id' => 'required'
        ]);

        $data = $request->all();

        $reportService = new ReportFinanceService();
        $comission = $reportService->getResumeCashbacks($data);

        return response()->json(['data' => $comission]);
    }

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
