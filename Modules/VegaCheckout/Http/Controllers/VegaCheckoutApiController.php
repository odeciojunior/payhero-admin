<?php

namespace Modules\VegaCheckout\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ApiToken;
use Modules\Core\Entities\Webhook;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Webhooks\Transformers\WebhooksResource;
use Modules\Integrations\Transformers\ApiTokenResource;

class VegaCheckoutApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            activity()->on((new Webhook()))->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos as integrações Vega Checkout');

            $Webhook = Webhook::where('company_id', auth()->user()->company_default)
                    ->where('description', 'Vega_Checkout')->get();
            $ApiToken = ApiToken::where('company_id', auth()->user()->company_default)
                    ->where('description', 'Vega_Checkout')->get();

            return response()->json([
                "integrations" => ApiTokenResource::collection($ApiToken),
                "Webhooks" => WebhooksResource::collection($Webhook),
            ]);
        } catch (Exception $e) {
            return response()->json([$e], 400);
        }
    }

    /**
     * @param $id
     * @return ReportanaResource
     */
    public function show($id)
    {
        try {
            $ApiTokenModel = new ApiToken();
            $ApiToken = $ApiTokenModel->find(current(Hashids::decode($id)));

            activity()
                ->on($ApiTokenModel)
                ->tap(function (Activity $activity) use ($id) {
                    $activity->log_name = "visualization";
                    $activity->subject_id = current(Hashids::decode($id));
                })
                ->log(
                    "Visualizou tela editar configurações de integração " .
                        $ApiToken->description .
                        " "
                );

            return new ApiTokenResource($ApiToken);
        } catch (Exception $e) {
            return response()->json(["message" => "Ocorreu algum erro"], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $ApiTokenModel = new ApiToken();
            $WebhooksModel = new Webhook();

            $ApiToken = $ApiTokenModel
                    ->where("description", 'Vega_Checkout')
                    ->where("company_id", auth()->user()->company_default)
                    ->where("deleted_at", null)
                    ->first();

            if (!empty($ApiToken)) {
                
                $integrationCreated = $WebhooksModel->firstOrCreate([
                    "user_id" => auth()->user()->account_owner_id,
                    "company_id" => auth()->user()->company_default,
                    "description" => 'Vega_Checkout',
                    "url" => 'https://pay.vegacheckout.com.br/api/postback/azcend',                    
                ]);

                if ($integrationCreated) {
                    return response()->json(
                        [
                            "message" => "Integração criada com sucesso!",
                        ],
                        200
                    );
                } else {
                    return response()->json(
                        [
                            "message" => "Ocorreu um erro ao realizar a integração ",
                        ],
                        400
                    );
                }
            } else {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro ao realizar a integração",
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            Log::warning("Erro ao realizar integração Vega Checkout - store");
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao realizar a integração",
                ],
                400
            );
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        try {
            if (!empty($id)) {
                $ApiTokenModel = new ApiToken();
                $WebhookModel = new Webhook();

                activity()
                    ->on($ApiTokenModel)
                    ->tap(function (Activity $activity) use ($id) {
                        $activity->log_name = "visualization";
                        $activity->subject_id = current(Hashids::decode($id));
                    })
                    ->log("Visualizou tela editar configurações da integração Vega Checkout");

                $Webhook = $WebhookModel->where("description", 'Vega_Checkout')->first();
              
                $integration = $ApiTokenModel->where("id", $id)->first();

                if ($integration) {
                    return response()->json(["Webhook" => $Webhook, "integration" => $ApiTokenModel]);
                } else {
                    return response()->json(
                        [
                            "message" => "Ocorreu um erro, tente novamente mais tarde!",
                        ],
                        400
                    );
                }
            } else {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro, tente novamente mais tarde!",
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            Log::warning("Erro ao tentar acessar tela editar Integração Vega Checkout (GeradorRastreioController - edit)");
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro, tente novamente mais tarde!",
                ],
                400
            );
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $WebhooksModel = new Webhook();
            $data = $request->all();
            $integrationId = current(Hashids::decode($id));
            $Webhook = $WebhooksModel->find($integrationId);
            $messageError = "";
            if (empty($data["clientid"])) {
                return response()->json(["message" => "CLIENT ID é obrigatório!"], 400);
            }

            $integrationUpdated = $Webhook->update([
                "clientid" => $data["clientid"],                
            ]);
            if ($integrationUpdated) {
                return response()->json(
                    [
                        "message" => "Integração atualizada com sucesso!",
                    ],
                    200
                );
            }

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao atualizar a integração",
                ],
                400
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao atualizar a integração",
                ],
                400
            );
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $integrationId = current(Hashids::decode($id));
            $ApitokenModel = new ApiToken();
            $WebhookModel = new Webhook();
            $integration = $ApitokenModel->where("id", $integrationId)->first();
            $Webhook = $WebhookModel->where("description", 'Vega_Checkout')->first();
            if (empty($integration)) {
                return response()->json(
                    [
                        "message" => "Erro ao tentar encontrar integração para remover",
                    ],
                    400
                );
            } else {
                $integrationDeleted = $integration->delete();
                $WebhookDeleted = $Webhook->delete();
                if ($integrationDeleted && $WebhookDeleted) {
                    return response()->json(
                        [
                            "message" => "Integração Removida com sucesso!",
                        ],
                        200
                    );
                }

                return response()->json(
                    [
                        "message" => "Erro ao tentar remover Integração",
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            Log::warning("Erro ao tentar remover Integração Vega Checkout");
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao tentar remover, tente novamente mais tarde!",
                ],
                400
            );
        }
    }
}
