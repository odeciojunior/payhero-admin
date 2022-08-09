<?php

namespace Modules\Sales\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\SaleService;
use Modules\Sales\Transformers\SalesExternalResource;
use Vinkla\Hashids\Facades\Hashids;

class ProfitfyApiController extends Controller
{
    public function index()
    {
        try {
            $salesModel = new Sale();
            $saleService = new SaleService();
            $companiesModel = new Company();
            //Conta as  requisições diárias da Profitfy
            $log = settings()
                ->group("profitfy_requests")
                ->get(now()->format("Y-m-d"), true);
            settings()
                ->group("profitfy_requests")
                ->set(now()->format("Y-m-d"), ($log ?? 0) + 1);
            $user = auth()->user();
            if (!empty($user)) {
                $userId = $user->account_owner_id;
                $saleStatus = [
                    $salesModel->present()->getStatus("approved"),
                    $salesModel->present()->getStatus("pending"),
                ];
                $sales = $salesModel
                    ->with(["transactions", "productsPlansSale.product"])
                    ->where("owner_id", $userId)
                    ->whereDate("start_date", ">=", now()->subDays(30))
                    ->whereIn("status", $saleStatus)
                    ->paginate(100);
                $userCompanies = $companiesModel->where("user_id", $userId)->pluck("id");
                foreach ($sales as $sale) {
                    $saleService->getDetails($sale, $userCompanies);
                    $products = [];
                    foreach ($sale->productsPlansSale as $productPlanSale) {
                        $product = $productPlanSale->product;
                        $products[] = [
                            "id" => $product->shopify_id,
                            "variant_id" => $product->shopify_variant_id,
                            "quantity" => $productPlanSale->amount,
                        ];
                    }
                    $sale->products = $products;
                }
                return SalesExternalResource::collection($sales);
            } else {
                return response()->json(["error" => "Usuário não autenticado"], 401);
            }
        } catch (Exception $e) {
            report($e);
            return response()->json(["error" => "Erro ao obter vendas"], 400);
        }
    }

    public function show($saleId)
    {
        try {
            $salesModel = new Sale();
            $saleService = new SaleService();
            $companiesModel = new Company();
            //Conta as  requisições diárias da Profitfy
            $log = settings()
                ->group("profitfy_requests")
                ->get(now()->format("Y-m-d"), true);
            settings()
                ->group("profitfy_requests")
                ->set(now()->format("Y-m-d"), ($log ?? 0) + 1);
            $user = auth()->user();
            if (!empty($user)) {
                $saleId = current(Hashids::connection("sale_id")->decode($saleId));
                $sale = $salesModel
                    ->with(["transactions", "productsPlansSale.product"])
                    ->where("id", $saleId)
                    ->where("owner_id", $user->account_owner_id)
                    ->first();
                if (!empty($sale)) {
                    $userCompanies = $companiesModel->where("user_id", $sale->owner_id)->pluck("id");
                    $saleService->getDetails($sale, $userCompanies);
                    $products = [];
                    foreach ($sale->productsPlansSale as $productPlanSale) {
                        $product = $productPlanSale->product;
                        $products[] = [
                            "id" => $product->shopify_id,
                            "variant_id" => $product->shopify_variant_id,
                            "quantity" => $productPlanSale->amount,
                        ];
                    }
                    $sale->products = $products;
                    return new SalesExternalResource($sale);
                } else {
                    return response()->json(["error" => "A venda não foi encontrada"], 404);
                }
            } else {
                return response()->json(["error" => "Usuário não autenticado"], 401);
            }
        } catch (Exception $e) {
            report($e);
            return response()->json(["error" => "Erro ao obter venda"], 400);
        }
    }
}
