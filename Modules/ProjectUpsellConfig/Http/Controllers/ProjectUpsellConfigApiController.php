<?php

namespace Modules\ProjectUpsellConfig\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\ProjectUpsellConfig;
use Modules\Core\Entities\ProjectUpsellRule;
use Modules\ProjectUpsellConfig\Transformers\PreviewUpsellResource;
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
        $projectId           = current(Hashids::decode($projectId));
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
        $projectId           = current(Hashids::decode($projectId));
        $projectUpsellConfig = new ProjectUpsellConfig();
        $data                = $request->all();
        if ($projectId) {
            $upsellConfig = $projectUpsellConfig->where('project_id', $projectId)->first();

            $upsellConfigUpdated = $upsellConfig->update([
                                                             'header'         => $data['header'],
                                                             'title'          => $data['title'],
                                                             'description'    => $data['description'],
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function previewUpsell(Request $request)
    {
        $projectUpsellConfigModel = new ProjectUpsellConfig();
        $projectUpsellRuleModel   = new ProjectUpsellRule();
        $data                     = $request->all();
        $projectId                = current(Hashids::decode($data['project_id']));
        if ($projectId) {
            $projectUpsellConfig                 = $projectUpsellConfigModel->where('project_id', $projectId)->first();
            $projectUpsellRule                   = $projectUpsellRuleModel->where('project_id', $projectId)->first();
            $projectUpsellConfig->apply_on_plans = $projectUpsellRule->apply_on_plans;
            $projectUpsellConfig->offer_on_plans = $projectUpsellRule->offer_on_plans;

            return new PreviewUpsellResource($projectUpsellConfig);
        } else {
            return response()->json([
                                        'message' => 'Erro carregar dados do upsell',
                                    ], 400);
        }
    }
}
