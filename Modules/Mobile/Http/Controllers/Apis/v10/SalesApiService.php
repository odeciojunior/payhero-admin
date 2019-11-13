<?php


namespace Modules\Mobile\Http\Controllers\Apis\v10;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\SaleService;
use Modules\Sales\Http\Requests\SaleIndexRequest;
use Modules\Sales\Transformers\SalesResource;
use Modules\Sales\Transformers\TransactionResource;

/**
 * Class SalesApiService
 * @package Modules\Mobile\Http\Controllers\Apis\v10
 */
class SalesApiService {

    /**
     * SalesApiService constructor.
     */
    public function __construct() {   }


    /**
     * @param SaleIndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function salesByFilter(SaleIndexRequest $request)
    {
        try {
            $saleService = new SaleService();
            $data = $request->all();
            $sales = $saleService->getSales($data);
            $salesCollection = TransactionResource::collection($sales);

            return response()->json(compact('salesCollection'), 200);

        } catch (Exception $e) {
            Log::warning('Erro ao buscar vendas SalesApiService - salesByFilter');
            report($e);

            return response()->json(['message' => 'Erro ao carregar vendas - SalesApiService - salesByFilter'], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse|SalesResource
     * @throws \Exception
     */
    public function saleById($id)
    {
        try {
            $saleService = new SaleService();

            if (isset($id)) {
                $sale = $saleService->getSaleWithDetails($id);
                $saleResource =  new SalesResource($sale);

                return response()->json(compact('saleResource'), 200);
            }

            return response()->json(['error' => 'Erro ao exibir detalhes da venda'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao mostrar detalhes da venda  SalesApiService - saleById');
            report($e);

            return response()->json(['error' => 'Erro ao exibir detalhes da venda'], 400);
        }
    }
}
