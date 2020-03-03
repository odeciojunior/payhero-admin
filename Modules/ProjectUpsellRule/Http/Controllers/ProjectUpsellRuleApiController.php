<?php

namespace Modules\ProjectUpsellRule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\ProjectUpsellRule;
use Modules\ProjectUpsellRule\Http\Requests\ProjectUpsellStoreRequest;
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
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(ProjectUpsellStoreRequest $request)
    {
        $projectUpsellModel = new ProjectUpsellRule();
        $data               = $request->all();
        $projectId          = current(Hashids::decode($data['project_id']));
        if ($projectId) {
            $applyPlanArray = [];
            $offerPlanArray = [];
            foreach ($data['apply_on_plans'] as $key => $value) {
                $applyPlanArray[] = $value;
            }
            foreach ($data['offer_on_plans'] as $key => $value) {
                $offerPlanArray[] = $value;
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
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
