<?php

namespace Modules\Reports\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Services\Reports\ReportMarketingService;
use Modules\Reports\Transformers\SalesByOriginResource;

class ReportsMarketingApiController extends Controller
{
    public function getResume(Request $request)
    {
        try {
            $request->validate([
                "date_range" => "required",
                "project_id" => "required",
            ]);

            $data = $request->all();

            $reportService = new ReportMarketingService();
            $resume = $reportService->getResumeMarketing($data);

            return response()->json([
                "data" => $resume,
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao obter dados de comissões"], 400);
        }
    }

    public function getSalesByState(Request $request)
    {
        try {
            $request->validate([
                "date_range" => "required",
                "project_id" => "required",
            ]);

            $data = $request->all();

            $reportService = new ReportMarketingService();
            $resume = $reportService->getSalesByState($data);

            return response()->json([
                "data" => $resume,
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao obter dados para o mapa"], 400);
        }
    }

    public function getMostFrequentSales(Request $request)
    {
        try {
            $request->validate([
                "date_range" => "required",
                "project_id" => "required",
            ]);

            $data = $request->all();

            $reportService = new ReportMarketingService();
            $mostFrequentSales = $reportService->getMostFrequentSales($data);

            return response()->json([
                "data" => $mostFrequentSales,
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao obter vendas mais frequentes"], 400);
        }
    }

    public function getDevices(Request $request)
    {
        try {
            $request->validate([
                "date_range" => "required",
                "project_id" => "required",
            ]);

            $data = $request->all();

            $reportService = new ReportMarketingService();
            $devices = $reportService->getDevices($data);

            return response()->json([
                "data" => $devices,
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao obter dados de dispositivos"], 400);
        }
    }

    public function getOperationalSystems(Request $request)
    {
        try {
            $request->validate([
                "date_range" => "required",
                "project_id" => "required",
            ]);

            $data = $request->all();

            $reportService = new ReportMarketingService();
            $devices = $reportService->getOperationalSystems($data);

            return response()->json([
                "data" => $devices,
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao obter dados de sistemas operacionais"], 400);
        }
    }

    public function getStateDetail(Request $request)
    {
        try {
            $request->validate([
                "date_range" => "required",
                "state" => "required",
            ]);

            $data = $request->all();

            $reportService = new ReportMarketingService();
            $stateDetail = $reportService->getStateDetail($data);

            return response()->json([
                "data" => $stateDetail,
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao obter detalhes do estado"], 400);
        }
    }

    public function getResumeCoupons(Request $request)
    {
        try {
            $request->validate([
                "date_range" => "required",
                "project_id" => "required",
            ]);

            $data = $request->all();
            $reportService = new ReportMarketingService();
            $coupons = $reportService->getResumeCoupons($data);

            return response()->json([
                "data" => $coupons,
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao obter dados de cupons de desconto"], 400);
        }
    }

    public function getResumeRegions(Request $request)
    {
        try {
            $request->validate([
                "date_range" => "required",
                "project_id" => "required",
            ]);

            $data = $request->all();

            $reportService = new ReportMarketingService();
            $regions = $reportService->getResumeRegions($data);

            return response()->json([
                "data" => $regions,
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao obter dados de regiões"], 400);
        }
    }

    public function getResumeOrigins(Request $request)
    {
        try {
            $request->validate([
                "paginate" => "required",
                "date_range" => "required",
                "origin" => "required",
                "project_id" => "required",
            ]);

            $data = $request->all();

            $reportService = new ReportMarketingService();
            $orders = $reportService->getResumeOrigins($data);

            $cacheName = "origins-resume-" . json_encode($data);
            return cache()->remember($cacheName, 120, function () use ($orders, $data) {
                if ($data["paginate"] === "false") {
                    if ($data["limit"] == "all") {
                        return SalesByOriginResource::collection($orders->get());
                    }

                    return SalesByOriginResource::collection($orders->limit($data["limit"])->get());
                }

                return SalesByOriginResource::collection($orders->paginate(10));
            });
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao obter dados de UTMs"], 400);
        }
    }
}
