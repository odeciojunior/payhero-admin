<?php

namespace Modules\ProjectReviewsConfig\Http\Controllers;

use Modules\Core\Entities\Project;
use Modules\ProjectReviewsConfig\Http\Requests\ProjectReviewsConfigUpdate;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\ProjectReviewsConfig;
use Modules\ProjectReviewsConfig\Transformers\ProjectReviewsConfigResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ProjectReviewsConfigApiController
 * @package Modules\ProjectReviewsConfig\Http\Controllers
 */
class ProjectReviewsConfigApiController extends Controller
{
    /**
     * @param $projectId
     * @return JsonResponse|ProjectReviewsConfigResource
     */
    public function show($projectId)
    {
        try {
            $projectId = current(Hashids::decode($projectId));
            if ($projectId) {
                $projectModel = new Project();
                $config = $projectModel->where("id", $projectId)->first();

                return new ProjectReviewsConfigResource($config);
            } else {
                return response()->json(
                    [
                        "message" => "Configuração da Review do Projeto não encontrado",
                    ],
                    404
                );
            }
        } catch (Exception $e) {
            return response()->json(
                [
                    "message" => "Erro ao buscar a Configuração da Review do Projeto",
                ],
                500
            );
        }
    }

    /**
     * @param ProjectReviewsConfigUpdate $request
     * @param $projectId
     * @return JsonResponse
     */
    public function update(ProjectReviewsConfigUpdate $request, $projectId): JsonResponse
    {
        try {
            $projectId = current(Hashids::decode($projectId));
            $data = $request->validated();
            if (empty($projectId)) {
                return response()->json(
                    [
                        "message" => "Erro ao atualizar configurações das reviews",
                    ],
                    404
                );
            }

            $project = Project::where("id", $projectId)->first();
            $projectReviewsConfigUpdated = $project->update($data);

            if ($projectReviewsConfigUpdated) {
                return response()->json(["message" => "Configuração das reviews atualizado com sucesso!"]);
            } else {
                return response()->json(
                    [
                        "message" => "Erro ao atualizar configurações das reviews",
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    "message" => "Erro ao atualizar configurações das reviews",
                ],
                500
            );
        }
    }
}
