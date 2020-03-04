<?php

namespace Modules\ProjectUpsellRule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\ProjectUpsellRule;
use Modules\ProjectUpsellRule\Http\Requests\ProjectUpsellStoreRequest;
use Modules\ProjectUpsellRule\Http\Requests\ProjectUpsellUpdateRequest;
use Modules\ProjectUpsellRule\Transformers\ProjectsUpsellResource;
use Vinkla\Hashids\Facades\Hashids;

class ProjectUpsellRuleApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('projectupsellrule::index');
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
            foreach ($data['apply_on_plans'] as $key => $value) {
                $applyPlanArray[] = current(Hashids::decode($value));
            }
            foreach ($data['offer_on_plans'] as $key => $value) {
                $offerPlanArray[] = current(Hashids::decode($value));
            }
            $applyPlanEncoded = json_encode($applyPlanArray);
            $offerPlanEncoded = json_encode($offerPlanArray);

            $projectUpsellModel->create([
                                            'project_id'     => $projectId,
                                            'description'    => $data['description'],
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
            foreach ($data['apply_on_plans'] as $key => $value) {
                $applyPlanArray[] = current(Hashids::decode($value));
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
