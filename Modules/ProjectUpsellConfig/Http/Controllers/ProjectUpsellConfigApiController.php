<?php

namespace Modules\ProjectUpsellConfig\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\ProjectUpsellConfig;
use Modules\ProjectUpsellConfig\Transformers\ProjectUpsellConfigResource;
use Vinkla\Hashids\Facades\Hashids;

class ProjectUpsellConfigApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('projectupsellconfig::index');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @param $projectId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
                                        'message' => 'Erro ao carregar dados de configurações de upsell',
                                    ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('projectupsellconfig::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
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
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
