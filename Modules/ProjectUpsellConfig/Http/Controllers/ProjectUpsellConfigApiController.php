<?php

namespace Modules\ProjectUpsellConfig\Http\Controllers;

use App\Http\Requests\ProjectUpsellConfigUpdate;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\ProjectUpsellConfig;
use Modules\Core\Services\CacheService;
use Modules\Core\Services\FoxUtils;
use Modules\ProjectUpsellConfig\Transformers\ProjectUpsellConfigResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ProjectUpsellConfigApiController
 * @package Modules\ProjectUpsellConfig\Http\Controllers
 */
class ProjectUpsellConfigApiController extends Controller
{
    /**
     * @param $projectId
     * @return JsonResponse|ProjectUpsellConfigResource
     */
    public function show($projectId)
    {
        try {
            $projectId = current(Hashids::decode($projectId));

            if ($projectId) {
                $upsellConfig = ProjectUpsellConfig::where("project_id", $projectId)->first();

                if (FoxUtils::isProduction()) {
                    $domain = Domain::select("name")
                        ->where("status", Domain::STATUS_APPROVED)
                        ->where("project_id", $projectId)
                        ->orderByDesc("id")
                        ->first();

                    $upsellConfig->checkoutUrl =
                        "https://checkout." . (isset($domain->name) ? $domain->name : "azcend.vip");
                } else {
                    $upsellConfig->checkoutUrl = env("CHECKOUT_URL", "http://dev.checkout.net");
                }

                return new ProjectUpsellConfigResource($upsellConfig);
            }

            return response()->json(["message" => "Projeto não encontrado"], 400);
        } catch (Exception $e) {
            return response()->json(["message" => "Projeto não encontrado"], 400);
        }
    }

    /**
     * @param ProjectUpsellConfigUpdate $request
     * @param $projectId
     * @return JsonResponse
     */
    public function update(ProjectUpsellConfigUpdate $request, $projectId)
    {
        try {
            $projectId = current(Hashids::decode($projectId));
            $projectUpsellConfig = new ProjectUpsellConfig();
            $data = $request->validated();
            if (empty($projectId)) {
                return response()->json(
                    [
                        "message" => "Erro ao atualizar configurações do upsell",
                    ],
                    400
                );
            }

            $upsellConfig = $projectUpsellConfig->where("project_id", $projectId)->first();

            $upsellConfigUpdated = $upsellConfig->update([
                "header" => $data["header"],
                "countdown_time" => $data["countdown_time"],
                "countdown_flag" => !empty($data["countdown_flag"]),
            ]);
            CacheService::forget(CacheService::UPSELL_DATA, $projectId);

            if ($upsellConfigUpdated) {
                return response()->json(["message" => "Configuração do upsell atualizado com sucesso!"], 200);
            } else {
                return response()->json(
                    [
                        "message" => "Erro ao atualizar configurações do upsell",
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    "message" => "Erro ao atualizar configurações do upsell",
                ],
                400
            );
        }
    }
}
