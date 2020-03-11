<?php

namespace Modules\ProjectUpsellRule\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ProjectUpsellRule;
use Modules\ProjectUpsellRule\Http\Requests\ProjectUpsellStoreRequest;
use Modules\ProjectUpsellRule\Http\Requests\ProjectUpsellUpdateRequest;
use Modules\ProjectUpsellRule\Transformers\ProjectsUpsellResource;
use Vinkla\Hashids\Facades\Hashids;

class ProjectUpsellRuleApiController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProjectUpsellStoreRequest $request)
    {
        $projectUpsellModel = new ProjectUpsellRule();
        $data               = $request->validated();
        $projectId          = current(Hashids::decode($data['project_id']));
        if ($projectId) {
            $applyPlanArray = [];
            $offerPlanArray = [];
            if (in_array('all', $data['apply_on_plans'])) {
                $applyPlanArray[] = 'all';
            } else {
                foreach ($data['apply_on_plans'] as $key => $value) {
                    $applyPlanArray[] = current(Hashids::decode($value));
                }
            }

            foreach ($data['offer_on_plans'] as $key => $value) {
                $offerPlanArray[] = current(Hashids::decode($value));
            }
            $applyPlanEncoded = json_encode($applyPlanArray);
            $offerPlanEncoded = json_encode($offerPlanArray);

            $projectUpsellModel->create([
                                            'project_id'     => $projectId,
                                            'description'    => $data['description'],
                                            'discount'       => $data['discount'],
                                            'active_flag'    => !empty($data['active_flag']) ? $data['active_flag'] : 0,
                                            'apply_on_plans' => $applyPlanEncoded,
                                            'offer_on_plans' => $offerPlanEncoded,
                                        ]);

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
     * @return Response
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

        return view('projectupsellrule::show');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
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
     * @param ProjectUpsellUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProjectUpsellUpdateRequest $request, $id)
    {
        $projectUpsellModel = new ProjectUpsellRule();
        $data               = $request->validated();
        $upsellId           = current(Hashids::decode($id));
        if ($upsellId) {
            $upsell         = $projectUpsellModel->find($upsellId);
            $applyPlanArray = [];
            $offerPlanArray = [];
            if (in_array('all', $data['apply_on_plans'])) {
                $applyPlanArray[] = 'all';
            } else {
                foreach ($data['apply_on_plans'] as $key => $value) {
                    $applyPlanArray[] = current(Hashids::decode($value));
                }
            }
            foreach ($data['offer_on_plans'] as $key => $value) {
                $offerPlanArray[] = current(Hashids::decode($value));
            }
            $applyPlanEncoded = json_encode($applyPlanArray);
            $offerPlanEncoded = json_encode($offerPlanArray);

            $upsellUpdated = $upsell->update([
                                                 'description'    => $data['description'],
                                                 'active_flag'    => !empty($data['active_flag']) ? $data['active_flag'] : 0,
                                                 'apply_on_plans' => $applyPlanEncoded,
                                                 'offer_on_plans' => $offerPlanEncoded,
                                             ]);
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
     * @return Response
     */
    public function destroy($id)
    {
        $projectUpsellModel = new ProjectUpsellRule();
        $upsellId           = current(Hashids::decode($id));
        if ($upsellId) {
            $upsell        = $projectUpsellModel->find($upsellId);
            $upsellDeleted = $upsell->delete();
            if ($upsellDeleted) {
                return response()->json(['message' => 'Upsell deletado com sucesso!'], 200);
            } else {
                return response()->json([
                                            'message' => 'Erro ao deletar upsell',
                                        ], 400);
            }
        } else {
            return response()->json([
                                        'message' => 'Erro ao deletar upsell',
                                    ], 400);
        }
    }
}
