<?php

namespace Modules\Reports\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Services\Reports\ReportSaleService;

class ReportsSaleApiController extends Controller
{
    public function getResumeSales(Request $request)
    {
        try {
            $request->validate([
                'date_range' => 'required',
                'project_id' => 'required'
            ]);

            $data = $request->all();

            $reportService = new ReportSaleService();
            $sales = $reportService->getResumeSales($data);

            return response()->json(['data' => $sales]);
        }
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de comissões'], 400);
        }
    }

    public function getResumeTypePayments(Request $request)
    {
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de tipos de pagamento'], 400);
        }
    }

    public function getResumeProducts(Request $request)
    {
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de produtos'], 400);
        }
    }

    public function getSalesResume(Request $request)
    {
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de comissões'], 400);
        }
    }

    public function getSalesDistribuitions(Request $request)
    {
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de comissões'], 400);
        }
    }

    public function getAbandonedCarts(Request $request)
    {
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de carrinhos abandonados'], 400);
        }
    }

    public function getOrderBump(Request $request)
    {
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de order bump'], 400);
        }
    }

    public function getUpsell(Request $request)
    {
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de upsell'], 400);
        }
    }

    public function getConversion(Request $request)
    {
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de conversão'], 400);
        }
    }

    public function getRecurrence(Request $request)
    {
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de pagamentos recorrentes'], 400);
        }
    }
}
