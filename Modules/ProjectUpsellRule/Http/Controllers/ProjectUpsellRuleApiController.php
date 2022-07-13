<?php

namespace Modules\ProjectUpsellRule\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ProjectUpsellRule;
use Modules\Core\Services\CacheService;
use Modules\ProjectUpsellRule\Http\Requests\ProjectUpsellStoreRequest;
use Modules\ProjectUpsellRule\Http\Requests\ProjectUpsellUpdateRequest;
use Modules\ProjectUpsellRule\Transformers\ProjectsUpsellResource;
use Vinkla\Hashids\Facades\Hashids;

class ProjectUpsellRuleApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $data               = $request->all();
            $projectUpsellModel = new ProjectUpsellRule();
            $projectId          = current(Hashids::decode($data['project_id']));
            if ($projectId) {
                $projectUpsell = $projectUpsellModel->where('project_id', $projectId);

                return ProjectsUpsellResource::collection($projectUpsell->paginate(5));
            } else {
                return response()->json([
                                            'message' => 'Erro ao listar dados de upsell',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar upsell (ProjectUpsellRuleApiController - index)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao listar dados de upsell',
                                    ], 400);
        }
    }

    /**
     * @param ProjectUpsellStoreRequest $request
     * @return JsonResponse
     */
    public function store(ProjectUpsellStoreRequest $request)
    {
        $projectUpsellModel = new ProjectUpsellRule();
        $data               = $request->validated();
        $projectId          = current(Hashids::decode($data['project_id']));
        if ($projectId) {
            $applyShippingArray = [];
            if (in_array('all', $data['apply_on_shipping'])) {
                $applyShippingArray[] = 'all';
            } else {
                foreach ($data['apply_on_shipping'] as $value) {
                    $applyShippingArray[] = current(Hashids::decode($value));
                }
            }

            $applyPlanArray = [];
            if (in_array('all', $data['apply_on_plans'])) {
                $applyPlanArray[] = 'all';
            } else {
                foreach ($data['apply_on_plans'] as $value) {
                    $applyPlanArray[] = current(Hashids::decode($value));
                }
            }

            $offerPlanArray = [];
            foreach ($data['offer_on_plans'] as $value) {
                $offerPlanArray[] = current(Hashids::decode($value));
            }

            $projectUpsellModel->create([
                'project_id' => $projectId,
                'description' => $data['description'],
                'discount' => !empty($data['discount']) ? $data['discount'] : 0,
                'active_flag' => !empty($data['active_flag']) ? $data['active_flag'] : 0,
                'use_variants' => !empty($data['use_variants']) ? $data['use_variants'] : 0,
                'apply_on_shipping' => json_encode($applyShippingArray),
                'apply_on_plans' => json_encode($applyPlanArray),
                'offer_on_plans' => json_encode($offerPlanArray),
            ]);

            CacheService::forget(CacheService::UPSELL_DATA, $projectId);

            return response()->json(['message' => 'Upsell criado com sucesso!'], 200);
        } else {
            return response()->json([
                                        'message' => 'Erro ao criar upsell',
                                    ], 400);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return JsonResponse|ProjectsUpsellResource
     */
    public function show($id)
    {
        $projectUpsellModel = new ProjectUpsellRule();
        $upsellId           = current(Hashids::decode($id));
        if ($upsellId) {
            $upsell = $projectUpsellModel->find($upsellId);

            return new ProjectsUpsellResource($upsell);
        } else {
            return response()->json([
                                        'message' => 'Erro ao carregar dados do upsell',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse|ProjectsUpsellResource
     */
    public function edit($id)
    {
        $projectUpsellModel = new ProjectUpsellRule();
        $upsellId           = current(Hashids::decode($id));
        if ($upsellId) {
            $upsell = $projectUpsellModel->find($upsellId);

            return new ProjectsUpsellResource($upsell);
        }

        return response()->json(['message' => 'Erro ao carregar dados do upsell'], 400);        
    }

    /**
     * @param ProjectUpsellUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ProjectUpsellUpdateRequest $request, $id)
    {
        $projectUpsellModel = new ProjectUpsellRule();
        $data               = $request->validated();
        $upsellId           = current(Hashids::decode($id));
        if ($upsellId) {
            $upsell         = $projectUpsellModel->find($upsellId);
            $applyShippingArray = [];
            if (in_array('all', $data['apply_on_shipping'])) {
                $applyShippingArray[] = 'all';
            } else {
                foreach ($data['apply_on_shipping'] as $value) {
                    $applyShippingArray[] = current(Hashids::decode($value));
                }
            }

            $applyPlanArray = [];
            if (in_array('all', $data['apply_on_plans'])) {
                $applyPlanArray[] = 'all';
            } else {
                foreach ($data['apply_on_plans'] as $value) {
                    $applyPlanArray[] = current(Hashids::decode($value));
                }
            }

            $offerPlanArray = [];
            foreach ($data['offer_on_plans'] as $value) {
                $offerPlanArray[] = current(Hashids::decode($value));
            }

            $upsellUpdated = $upsell->update([
                'description' => $data['description'],
                'discount' => !empty($data['discount']) ? $data['discount'] : 0,
                'active_flag' => !empty($data['active_flag']) ? $data['active_flag'] : 0,
                'use_variants' => !empty($data['use_variants']) ? $data['use_variants'] : 0,
                'apply_on_shipping' => json_encode($applyShippingArray),
                'apply_on_plans' => json_encode($applyPlanArray),
                'offer_on_plans' => json_encode($offerPlanArray),
            ]);

            CacheService::forget(CacheService::UPSELL_DATA, $upsell->project_id);

            if ($upsellUpdated) {
                return response()->json(['message' => 'Upsell atualizado com sucesso!'], 200);
            } else {
                return response()->json([
                                            'message' => 'Erro ao atualizar upsell',
                                        ], 400);
            }
        } else {
            return response()->json([
                                        'message' => 'Erro ao atualizar upsell',
                                    ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {

            if (empty($id)) {
                return response()->json([
                                            'message' => 'Erro ao deletar upsell',
                                        ], 400);
            }

            $projectUpsellModel = new ProjectUpsellRule();
            $upsell             = $projectUpsellModel->find(current(Hashids::decode($id)));

            CacheService::forget(CacheService::UPSELL_DATA, $upsell->project_id);

            if (!empty($upsell)) {
                $upsellDeleted = $upsell->delete();
                if ($upsellDeleted) {
                    return response()->json(['message' => 'Upsell deletado com sucesso!'], 200);
                }
            }

            return response()->json([
                                        'message' => 'Erro ao deletar upsell',
                                    ], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                                        'message' => 'Erro ao deletar upsell',
                                    ], 400);
        }
    }
}
