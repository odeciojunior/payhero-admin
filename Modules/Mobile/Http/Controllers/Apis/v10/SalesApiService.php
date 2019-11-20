<?php


namespace Modules\Mobile\Http\Controllers\Apis\v10;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Checkouts\Transformers\CheckoutResource;
use Modules\Clients\Transformers\ClientResource;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Client;
use Modules\Core\Entities\Delivery;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\SaleService;
use Modules\Deliveries\Transformers\DeliveryResource;
use Modules\Products\Transformers\ProductsSaleResource;
use Modules\Sales\Transformers\SalesResource;
use Modules\Sales\Transformers\TransactionResource;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

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
    public function salesByFilter(Request $request)
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


    public function getSaleDetails(Request $request) {

        try {
            /**
             * TODO: Colocar tudo em uma consulta com relacionamento
             */
            $sale = $this->saleById($request["saleCode"]);
            $client = $this->clientById($sale->client_id);
            $productsSale = $this->productBySale($request["saleCode"]);
            $delivery = $this->deliveryById($sale->delivery_id);
            $checkout = $this->checkoutById($sale->checkout_id);

            return response()->json(compact('sale', 'client', 'productsSale', 'delivery', 'checkout'), 200);

        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados da venda (SalesApiService - getSalesDetails)');
            report($e);

            return response()->json([
                'message' => 'Ocorreu um erro, tente novamente mais tarde',
            ], 400);
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

                return $saleResource;
            }

            return response()->json(['error' => 'Erro ao exibir detalhes da venda'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao mostrar detalhes da venda  SalesApiService - saleById');
            report($e);

            return response()->json(['error' => 'Erro ao exibir detalhes da venda'], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse|ClientResource
     */
    public function clientById($id)
    {
        try {

            if (!empty($id)) {

                $clientModel = new Client();

                $client = $clientModel->find($id);

                if (!empty($client)) {
                    return new ClientResource($client);
                } else {
                    return response()->json([
                        'message' => 'Ocorreu um erro, cliente não encontrado',
                    ], 400);
                }
            } else {
                // Hash invalido
                return response()->json([
                    'message' => 'Ocorreu um erro, cliente não encontrado',
                ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar cliente, (SalesApiService - clientById)');
            report($e);

            return response()->json([
                'message' => 'Ocorreu um erro, cliente não encontrado',
            ], 400);
        }
    }


    /**
     * @param $saleId
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function productBySale($saleId)
    {
        try {
            if ($saleId) {
                $productService = new ProductService();

                $products = $productService->getProductsBySale($saleId);

                return ProductsSaleResource::collection($products);
            } else {
                return response()->json(['message' => 'Erro ao tentar obter produtos'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar obter produtos (SalesApiService - getProductBySale)');
            report($e);

            return response()->json(['message' => 'Erro ao tentar obter produtos'], 400);
        }
    }



    public function deliveryById($deliveryId)
    {
        try {
            if (isset($deliveryId)) {
                $deliveryModel = new Delivery();

                $delivery = $deliveryModel->find($deliveryId);

                if (!empty($delivery)) {
                    return new DeliveryResource($delivery);
                } else {
                    return response()->json([
                        'message' => 'Ocorreu um erro,dados invalidos',
                    ], 400);
                }
            } else {
                return response()->json([
                    'message' => 'Ocorreu um erro,dados invalidos',
                ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados delivery (DeliveryApiController - show)');
            report($e);

            return response()->json([
                'message' => 'Ocorreu um erro, tente novamente mais tarde',
            ], 400);
        }
    }

    public function checkoutById($id)
    {
        try {
            if (isset($id)) {
                $checkoutModel = new Checkout();
                $checkout = $checkoutModel->find($id);

                return new CheckoutResource($checkout);
            } else {
                return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados recuperação de vendas (CheckoutApiController - index)');
            report($e);

            return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde'], 400);
        }
    }
}
