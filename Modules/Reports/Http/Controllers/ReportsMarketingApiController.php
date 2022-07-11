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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de comissões'], 400);
        }
    }

    public function getSalesByState(Request $request)
    {
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados para o mapa'], 400);
        }
    }

    public function getMostFrequentSales(Request $request)
    {
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter vendas mais frequentes'], 400);
        }            
    }

    public function getDevices(Request $request)
    {
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de dispositivos'], 400);
        }
    }

    public function getOperationalSystems(Request $request)
    {
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de sistemas operacionais'], 400);
        }
    }

    public function getStateDetail(Request $request)
    {
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter detalhes do estado'], 400);
        }
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
            return response()->json(['message' => 'Erro ao obter dados de cupons de desconto'], 400);
        }
    }

    public function getResumeRegions(Request $request)
    {
        return '{"data":[{"region":"01","access":2,"conversion":0,"percentage_access":0,"percentage_conversion":0},{"region":"15","access":1,"conversion":0,"percentage_access":0,"percentage_conversion":0},{"region":"AC","access":46,"conversion":7,"percentage_access":0.3,"percentage_conversion":0},{"region":"AL","access":144,"conversion":25,"percentage_access":0.8,"percentage_conversion":0.1},{"region":"AM","access":254,"conversion":48,"percentage_access":1.4,"percentage_conversion":0.3},{"region":"AP","access":50,"conversion":8,"percentage_access":0.3,"percentage_conversion":0},{"region":"ASU","access":1,"conversion":0,"percentage_access":0,"percentage_conversion":0},{"region":"BA","access":669,"conversion":124,"percentage_access":3.7,"percentage_conversion":0.7},{"region":"CE","access":483,"conversion":76,"percentage_access":2.7,"percentage_conversion":0.4},{"region":"DC","access":2,"conversion":0,"percentage_access":0,"percentage_conversion":0},{"region":"DF","access":342,"conversion":67,"percentage_access":1.9,"percentage_conversion":0.4},{"region":"ENG","access":2,"conversion":0,"percentage_access":0,"percentage_conversion":0},{"region":"ES","access":280,"conversion":57,"percentage_access":1.6,"percentage_conversion":0.3},{"region":"FL","access":1,"conversion":0,"percentage_access":0,"percentage_conversion":0},{"region":"GA","access":1,"conversion":0,"percentage_access":0,"percentage_conversion":0},{"region":"GO","access":514,"conversion":95,"percentage_access":2.8,"percentage_conversion":0.5},{"region":"HDF","access":1,"conversion":1,"percentage_access":0,"percentage_conversion":0},{"region":"HE","access":1,"conversion":1,"percentage_access":0,"percentage_conversion":0},{"region":"L","access":1,"conversion":0,"percentage_access":0,"percentage_conversion":0},{"region":"MA","access":283,"conversion":51,"percentage_access":1.6,"percentage_conversion":0.3},{"region":"MD","access":1,"conversion":0,"percentage_access":0,"percentage_conversion":0},{"region":"MG","access":1491,"conversion":268,"percentage_access":8.3,"percentage_conversion":1.5},{"region":"MS","access":246,"conversion":44,"percentage_access":1.4,"percentage_conversion":0.2},{"region":"MT","access":380,"conversion":81,"percentage_access":2.1,"percentage_conversion":0.5},{"region":"NJ","access":2,"conversion":0,"percentage_access":0,"percentage_conversion":0},{"region":"NW","access":1,"conversion":0,"percentage_access":0,"percentage_conversion":0},{"region":"OCC","access":1,"conversion":0,"percentage_access":0,"percentage_conversion":0},{"region":"PA","access":448,"conversion":95,"percentage_access":2.5,"percentage_conversion":0.5},{"region":"PB","access":188,"conversion":30,"percentage_access":1,"percentage_conversion":0.2},{"region":"PE","access":476,"conversion":87,"percentage_access":2.6,"percentage_conversion":0.5},{"region":"PI","access":119,"conversion":21,"percentage_access":0.7,"percentage_conversion":0.1},{"region":"PR","access":995,"conversion":219,"percentage_access":5.5,"percentage_conversion":1.2},{"region":"QC","access":9,"conversion":2,"percentage_access":0.1,"percentage_conversion":0},{"region":"RJ","access":1555,"conversion":345,"percentage_access":8.6,"percentage_conversion":1.9},{"region":"RN","access":157,"conversion":29,"percentage_access":0.9,"percentage_conversion":0.2},{"region":"RO","access":132,"conversion":25,"percentage_access":0.7,"percentage_conversion":0.1},{"region":"RR","access":14,"conversion":3,"percentage_access":0.1,"percentage_conversion":0},{"region":"RS","access":810,"conversion":193,"percentage_access":4.5,"percentage_conversion":1.1},{"region":"S","access":1,"conversion":0,"percentage_access":0,"percentage_conversion":0},{"region":"SC","access":694,"conversion":143,"percentage_access":3.8,"percentage_conversion":0.8},{"region":"SE","access":103,"conversion":13,"percentage_access":0.6,"percentage_conversion":0.1},{"region":"SP","access":4103,"conversion":812,"percentage_access":22.7,"percentage_conversion":4.5},{"region":"TO","access":84,"conversion":14,"percentage_access":0.5,"percentage_conversion":0.1},{"region":"TX","access":1,"conversion":0,"percentage_access":0,"percentage_conversion":0},{"region":"VA","access":1,"conversion":1,"percentage_access":0,"percentage_conversion":0}]}';
        try {
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
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de regiões'], 400);
        }
    }

    public function getResumeOrigins(Request $request)
    {
        try {
            $request->validate([
                'paginate' => 'required',
                'date_range' => 'required',
                'origin' => 'required',
                'project_id' => 'required',
            ]);

            $data = $request->all();

            $reportService = new ReportMarketingService();
            $orders = $reportService->getResumeOrigins($data);

            $cacheName = 'origins-resume-'.json_encode($data);
            return cache()->remember($cacheName, 120, function() use ($orders, $data) {
                if ($data['paginate'] === 'false') {
                    return SalesByOriginResource::collection($orders->limit(10)->get());
                }

                return SalesByOriginResource::collection($orders->paginate(10));
            });
        }
        catch(Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao obter dados de UTMs'], 400);
        }
    }
}
