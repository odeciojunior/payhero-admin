<?php

namespace Modules\Reports\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Services\Reports\ReportFinanceService;

class ReportsFinanceApiController extends Controller
{
    public function getResumeCommissions(Request $request)
    {
        try {
            $request->validate([
                'date_range' => 'required',
                'project_id' => 'required'
            ]);
    
            $data = $request->all();
    
            $reportService = new ReportFinanceService();
            $comission = $reportService->getResumeCommissions($data);
    
            return response()->json(['data' => $comission]);
        }
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de comissões'], 400);
        }
    }

    public function getResumePendings(Request $request)
    {
        try{
            $request->validate([
                'date_range' => 'required',
                'project_id' => 'required'
            ]);

            $data = $request->all();

            $reportService = new ReportFinanceService();
            $pending = $reportService->getResumePendings($data);

            return response()->json(['data' => $pending]);
        }
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de saldo pendente'], 400);
        }
    }

    public function getResumeCashbacks(Request $request)
    {
        try{
            $request->validate([
                'date_range' => 'required',
                'project_id' => 'required'
            ]);

            $data = $request->all();

            $reportService = new ReportFinanceService();
            $comission = $reportService->getResumeCashbacks($data);

            return response()->json(['data' => $comission]);
        }
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de cashbacks'], 400);
        }
    }

    public function getFinancesResume(Request $request)
    {
        try{
            $request->validate([ 'date_range' => 'required' ]);

            $data = $request->all();

            $reportService = new ReportFinanceService();
            $resume = $reportService->getFinancesResume($data);

            return response()->json([
                'data' => $resume
            ]);
        }
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de finanças'], 400);
        }
    }

    public function getFinancesCashbacks(Request $request)
    {
        try{
            $request->validate([ 'date_range' => 'required' ]);

            $data = $request->all();

            $reportService = new ReportFinanceService();
            $resume = $reportService->getFinancesCashbacks($data);

            return response()->json([
                'data' => $resume
            ]);
        }
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de cashbacks'], 400);
        }
    }

    public function getFinancesPendings(Request $request)
    {
        try {
            $reportService = new ReportFinanceService();
            $resume = $reportService->getFinancesPendings();

            return response()->json([
                'data' => $resume
            ]);
        }
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de saldo pendente'], 400);
        }
    }

    public function getFinancesBlockeds(Request $request)
    {
        try{
            $reportService = new ReportFinanceService();
            $resume = $reportService->getFinancesBlockeds();

            return response()->json([
                'data' => $resume
            ]);
        }
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de saldo retido'], 400);
        }
    }

    public function getFinancesDistribuitions(Request $request)
    {
        try {
            $reportService = new ReportFinanceService();
            $resume = $reportService->getFinancesDistribuitions();

            return response()->json([
                'data' => $resume
            ]);
        }
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de comissões'], 400);
        }
    }

    public function getFinancesWithdrawals(Request $request)
    {
        try {
            $reportService = new ReportFinanceService();
            $resume = $reportService->getFinancesWithdrawals();

            return response()->json([
                'data' => $resume
            ]);
        }
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de saques'], 400);
        }
    }
}
