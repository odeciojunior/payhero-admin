<?php

namespace Modules\Sales\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleRefundHistory;
use Modules\Core\Entities\SaleWoocommerceRequests;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\EmailService;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\ShopifyErrors;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\WooCommerceService;
use Modules\Plans\Transformers\PlansSelectResource;
use Modules\Sales\Exports\Reports\SaleReportExport;
use Modules\Sales\Http\Requests\SaleIndexRequest;
use Modules\Sales\Transformers\SalesResource;
use Modules\Sales\Transformers\TransactionResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class SalesApiController extends Controller
{
    public function index(SaleIndexRequest $request)
    {
        try {
            activity()
                ->tap(function (Activity $activity) {
                    $activity->log_name = "visualization";
                })
                ->log("Visualizou tela todas as vendas");

            $saleService = new SaleService();
            $data = $request->all();
            $sales = $saleService->getPaginatedSales($data);

            return TransactionResource::collection($sales);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao carregar vendas"], 400);
        }
    }

    public function show($id)
    {
        try {
            if (empty($id)) {
                return response()->json(["message" => "Erro ao exibir detalhes da venda"], 400);
            }
            activity()
                ->on(new Sale())
                ->tap(function (Activity $activity) use ($id) {
                    $activity->log_name = "visualization";
                    $activity->subject_id = hashids_decode($id, "sale_id");
                })
                ->log("Visualizou detalhes da venda #" . $id);

            $sale = (new SaleService())->getSaleWithDetails($id);
            if (!empty($sale->affiliate)) {
                $users = [$sale->owner_id, $sale->affiliate->user_id];
            } else {
                $users = [$sale->owner_id];
            }

            if (
                !in_array(
                    auth()
                        ->user()
                        ->getAccountOwnerId(),
                    $users,
                )
            ) {
                return response()->json(["message" => "Sem permissão para visualizar detalhes da venda"], 400);
            }

            return new SalesResource($sale);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => $e->getMessage()], 400); //'Erro ao exibir detalhes da venda'
        }
    }

    public function export(SaleIndexRequest $request)
    {
        try {
            $dataRequest = $request->all();
            activity()
                ->tap(function (Activity $activity) {
                    $activity->log_name = "visualization";
                })
                ->log("Exportou tabela " . $dataRequest["format"] . " de vendas");
            $user = auth()->user();
            $filename = "sales_report_" . hashids_encode($user->id) . ".csv"; //. $dataRequest['format'];
            (new SaleReportExport($dataRequest, $user, $filename))->queue($filename)->allOnQueue("high");
            return response()->json(["message" => "A exportação começou", "email" => $dataRequest["email"]]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao tentar gerar o arquivo Excel."], 200);
        }
    }

    public function resume(SaleIndexRequest $request)
    {
        try {
            activity()
                ->tap(function (Activity $activity) {
                    $activity->log_name = "visualization";
                })
                ->log("Visualizou tela exibir resumo das venda ");
            $saleService = new SaleService();
            $data = $request->all();
            $resume = $saleService->getResume($data);
            return response()->json($resume);
        } catch (Exception $e) {
            report($e);
            return response()->json(["error" => "Erro ao exibir resumo das vendas"], 400);
        }
    }

    public function refund(Request $request, $saleId)
    {
        try {
            $postData = request()->all();
            $saleIdDecoded = hashids_decode($saleId, "sale_id");
            $sale = Sale::find($saleIdDecoded);

            activity()
                ->on(new Sale())
                ->tap(function (Activity $activity) use ($saleIdDecoded) {
                    $activity->log_name = "estorno";
                    $activity->subject_id = $saleIdDecoded;
                })
                ->log("Tentativa estorno transação: #" . $saleId);

            $saleService = new SaleService();
            $data = $saleService->refund($sale, $postData["refund_observation"]);

            if ($data["status"] == "success") {
                return response()->json(
                    [
                        "status" => "success",
                        "message" => $data["message"],
                    ],
                    Response::HTTP_OK,
                );
            }

            return response()->json(
                [
                    "status" => "error",
                    "message" => $data["message"],
                ],
                Response::HTTP_BAD_REQUEST,
            );
        } catch (Exception $e) {
            report($e);
            return response()->json(
                ["status" => "error", "message" => "Erro ao tentar estornar venda."],
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function newOrderShopify(Request $request, $saleId)
    {
        try {
            if (foxutils()->isProduction()) {
                $result = false;
                $saleModel = new Sale();
                $sale = $saleModel->with("upsells")->find(hashids_decode($saleId, "sale_id"));
                $shopifyIntegration = ShopifyIntegration::where("project_id", $sale->project_id)->first();
                activity()
                    ->on($saleModel)
                    ->tap(function (Activity $activity) use ($saleId) {
                        $activity->log_name = "visualization";
                        $activity->subject_id = hashids_decode($saleId, "sale_id");
                    })
                    ->log("Gerou nova ordem no shopify para transação: #" . $saleId);

                if (!foxutils()->isEmpty($shopifyIntegration)) {
                    $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
                    $result = $shopifyService->newOrder($sale);
                    $shopifyService->saveSaleShopifyRequest();
                }

                if ($result["status"] == "success") {
                    return response()->json(["message" => $result["message"]], Response::HTTP_OK);
                }

                return response()->json(["message" => $result["message"]], Response::HTTP_BAD_REQUEST);
            }

            return response()->json(
                ["message" => "Funcionalidade habilitada somente em produção =)"],
                Response::HTTP_OK,
            );
        } catch (Exception $e) {
            $message = ShopifyErrors::FormatErrors($e->getMessage());
            if (empty($message)) {
                report($e);
                $message = "Erro ao tentar gerar ordem no Shopify.";
            }
            return response()->json(["message" => $message], Response::HTTP_BAD_REQUEST);
        }
    }

    public function newOrderWoocommerce(Request $request, $saleId)
    {
        try {
            if (foxutils()->isProduction()) {
                $saleModel = new Sale();
                $sale = $saleModel
                    ->with("upsells")
                    ->find(hashids_decode($saleId, "sale_id"))
                    ->first();
                $integration = WooCommerceIntegration::where("project_id", $sale->project_id)->first();

                activity()
                    ->on($saleModel)
                    ->tap(function (Activity $activity) use ($saleId) {
                        $activity->log_name = "visualization";
                        $activity->subject_id = hashids_decode($saleId, "sale_id");
                    })
                    ->log("Gerou nova ordem no woocommerce para transação: #" . $saleId);

                if (!foxutils()->isEmpty($integration)) {
                    $service = new WooCommerceService(
                        $integration->url_store,
                        $integration->token_user,
                        $integration->token_pass,
                    );

                    $request = SaleWoocommerceRequests::where("sale_id", $sale->id)
                        ->where("status", 0)
                        ->where("method", "CreatePendingOrder")
                        ->first();

                    if (!empty($request)) {
                        $data = json_decode($request["send_data"], true);

                        $changeToPaidStatus = 0;

                        if ($data["status"] == "processing" && $data["set_paid"] == true) {
                            $data["status"] = "pending";
                            $data["set_paid"] = false;
                            $changeToPaidStatus = 1;
                        }

                        $result = $service->woocommerce->post("orders", $data);

                        if ($result->id) {
                            $order = $result->id;
                            $saleModel = Sale::where("id", $request["sale_id"])->first();
                            $saleModel->woocommerce_order = $order;
                            $saleModel->save();

                            $result = json_encode($result);
                            $service->updatePostRequest($request["id"], 1, $result, $order);

                            if ($changeToPaidStatus == 1) {
                                $result = $service->approveBillet($order, $request["project_id"], $request["sale_id"]);
                            }

                            return response()->json(["message" => "Ordem criada com sucesso!"], Response::HTTP_OK);
                        }

                        return response()->json(
                            ["message" => "Erro ao tentar criar a ordem!"],
                            Response::HTTP_BAD_REQUEST,
                        );
                    }

                    return response()->json(["message" => "Requisição não encontrada!"], Response::HTTP_BAD_REQUEST);
                } else {
                    return response()->json(["message" => "Integração não encontrada"], Response::HTTP_BAD_REQUEST);
                }
            }

            return response()->json(
                ["message" => "Funcionalidade habilitada somente em produção =)"],
                Response::HTTP_OK,
            );
        } catch (Exception $e) {
            report($e);
            $message = "Erro ao tentar gerar ordem no Woocommerce.";

            return response()->json(["message" => $message], Response::HTTP_BAD_REQUEST);
        }
    }

    public function saleReSendEmail(Request $request)
    {
        try {
            $saleModel = new Sale();
            $sale = explode(" ", $request->input("sale"));
            $saleId = hashids_decode($sale[0], "sale_id");
            $sale = $saleModel->with(["customer", "project.checkoutConfig"])->find($saleId);
            if (empty($sale)) {
                return response()->json(["message" => "Erro ao reenviar email."], Response::HTTP_BAD_REQUEST);
            }
            activity()
                ->on($saleModel)
                ->tap(function (Activity $activity) use ($saleId, $request) {
                    $activity->log_name = "created";
                    $activity->subject_id = $saleId;
                })
                ->log("Reenviou email para a venda: #" . $request->input("sale"));
            EmailService::clientSale($sale->customer, $sale, $sale->project);
            return response()->json(["message" => "Email enviado"], Response::HTTP_OK);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao reenviar email."], Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateRefundObservation($id, Request $request)
    {
        try {
            $saleRefundHistoryModel = new SaleRefundHistory();
            $data = $request->all();
            $id = hashids_decode($id, "sale_id");
            if (!empty($id && !empty($data["name"]) && !empty($data["value"]))) {
                $saleRefundHistory = $saleRefundHistoryModel->where("sale_id", $id)->first();
                if (!empty($saleRefundHistory)) {
                    $saleRefundHistory->refund_observation = $data["value"];
                    $saleRefundHistory->save();
                    return response()->json(["message" => "Causa do estorno alterado com successo!"]);
                }
                return response()->json(["message" => "Venda não encontrada!"], 400);
            }

            return response()->json(["message" => "Os dados informados são inválidos!"], 400);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao alterar causa do estorno!"], 400);
        }
    }

    public function getPlans(Request $request)
    {
        try {
            $data = $request->all();

            $projectIds = [];
            if (!empty($data["project_id"])) {
                //if (is_array($data["project_id"])) {
                if (!empty($data["project_id"][0])) {
                    foreach ($data["project_id"] as $project) {
                        if (!empty($project)) {
                            array_push($projectIds, hashids_decode($project));
                        }
                    }
                } else {
                    $projects = SaleService::getProjectsWithSales();
                    foreach ($projects as $item) {
                        array_push($projectIds, $item->project_id);
                    }
                }
            }

            $user = auth()->user();
            $userId = $user->getAccountOwnerId();
            $plans = null;

            if (current($projectIds)) {
                if (!empty($data["search"])) {
                    $plans = Plan::where("name", "like", "%" . $data["search"] . "%")
                        ->whereIn("project_id", $projectIds)
                        ->orderby("name")
                        ->limit(30)
                        ->get();
                } else {
                    $plans = Plan::whereIn("project_id", $projectIds)
                        ->orderby("name")
                        ->limit(30);
                    $plans = $plans->get();
                }
                return PlansSelectResource::collection($plans);
            } else {
                $userProjects = SaleService::getProjectsWithSales();

                if (!empty($data["search"])) {
                    $plans = Plan::where("name", "like", "%" . $data["search"] . "%")
                        ->whereIn("project_id", $userProjects)
                        ->orderby("name")
                        ->limit(30)
                        ->get();
                } else {
                    $plans = Plan::whereIn("project_id", $userProjects)
                        ->orderby("name")
                        ->limit(30)
                        ->get();
                }
                return PlansSelectResource::collection($plans);
            }
        } catch (Exception $e) {
            report($e);
            return response()->json(
                [
                    "message" => "Ocorreu um erro, ao buscar dados dos planos",
                ],
                400,
            );
        }
    }

    public function setValueObservation(Request $request, $id)
    {
        try {
            if (!empty($id)) {
                $saleModel = new Sale();
                activity()
                    ->on($saleModel)
                    ->tap(function (Activity $activity) use ($id) {
                        $activity->log_name = "updated";
                        $activity->subject_id = hashids_decode($id, "sale_id");
                    })
                    ->log("Adicionou observação a venda #" . $id);
                $sale = $saleModel->find(hashids_decode($id, "sale_id"));
                $sale->update([
                    "observation" => $request->input("observation"),
                ]);

                return response()->json(
                    [
                        "message" => "Observaçao atualizada com sucesso!",
                        "id" => $sale->id,
                    ],
                    200,
                );
            }

            return response()->json(
                [
                    "message" => "Erro ao atualizar observaçao!",
                ],
                400,
            );
        } catch (Exception $e) {
            report($e);
            return response()->json(
                [
                    "message" => "Erro ao atualizar observaçao!",
                ],
                400,
            );
        }
    }

    public function getProjectsWithSales()
    {
        $rows = [];
        $projects = SaleService::getProjectsWithSalesAndTokens();
        foreach ($projects as $item) {
            $rows[] = [
                "project_id" => ($item->prefix ?? "") . Hashids::encode($item->project_id),
                "name" => $item->name,
            ];
        }

        return $rows;
    }
}
