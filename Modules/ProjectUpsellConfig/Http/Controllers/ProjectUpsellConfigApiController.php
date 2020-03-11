<?php

namespace Modules\ProjectUpsellConfig\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\ProjectUpsellConfig;
use Modules\ProjectUpsellConfig\Transformers\ProjectUpsellConfigResource;
use Vinkla\Hashids\Facades\Hashids;

class ProjectUpsellConfigApiController extends Controller
{
    /**
     * @param $projectId
     * @return JsonResponse|ProjectUpsellConfigResource
     */
    public function show($projectId)
    {
        $projectId = current(Hashids::decode($projectId));
        $projectUpsellConfig = new ProjectUpsellConfig();
        if ($projectId) {
            $upsellConfig = $projectUpsellConfig->where('project_id', $projectId)->first();

            return new ProjectUpsellConfigResource($upsellConfig);

        } else {
            return response()->json([
                'message' => 'Projeto não encontrado',
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $projectId
     * @return JsonResponse|Response
     */
    public function update(Request $request, $projectId)
    {
        $projectId = current(Hashids::decode($projectId));
        $projectUpsellConfig = new ProjectUpsellConfig();
        $data = $request->all();
        if ($projectId) {
            $upsellConfig = $projectUpsellConfig->where('project_id', $projectId)->first();

            $upsellConfigUpdated = $upsellConfig->update([
                'header' => $data['header'],
                'title' => $data['title'],
                'description' => $data['description'],
                'countdown_time' => $data['countdown_time'],
                'countdown_flag' => !empty($data['countdown_flag']) ? true : false,
            ]);
            if ($upsellConfigUpdated) {
                return response()->json(['message' => 'Configuração do upsell atualizado com sucesso!'], 200);
            } else {
                return response()->json([
                    'message' => 'Erro ao atualizar configurações do upsell',
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Erro ao atualizar configurações do upsell',
            ], 400);
        }
    }
}
