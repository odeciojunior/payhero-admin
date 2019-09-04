<?php

namespace Modules\ConvertaX\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\ConvertaxIntegration;

class ConvertaXController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('convertax::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        try {
            $userProjectModel = new UserProject();
            $projects         = [];
            $userProjects     = $userProjectModel->where('user_id', auth()->user()->id)->with('project')->get();
            if ($userProjects->count() > 0) {
                foreach ($userProjects as $userProject) {
                    $projects[] = $userProject->project;
                }

                return view('convertax::create', ['projects' => $projects]);
            } else {

                return response()->json([
                                            'message' => 'Nenhum projeto encontrado',
                                        ], 222);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar redirecionar para tela de adicionar integração (ConvertaXController - create)');
            report($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $data                      = $request->all();
            $convertaxIntegrationModel = new ConvertaxIntegration();

            $integration = $convertaxIntegrationModel->where('project_id', $data['project_id'])->first();
            if ($integration) {
                return response()->json([
                                            'message' => 'Projeto já integrado',
                                        ], 400);
            }
            if (empty($data['boleto_generated'])) {
                $data['boleto_generated'] = 0;
            }
            if (empty($data['boleto_paid'])) {
                $data['boleto_paid'] = 0;
            }
            if (empty($data['credit_card_paid'])) {

                $data['credit_card_paid'] = 0;
            }
            if (empty($data['credit_card_refused'])) {

                $data['credit_card_refused'] = 0;
            }
            if (empty($data['abandoned_cart'])) {

                $data['abandoned_cart'] = 0;
            }
            $data['value']      = preg_replace('/[.,]/', '', $data['value']);

            $integrationCreated = $convertaxIntegrationModel->create([
                                                                         'link'                => $data['link'],
                                                                         'value'               => $data['value'],
                                                                         'boleto_generated'    => $data['boleto_generated'],
                                                                         'boleto_paid'         => $data['boleto_paid'],
                                                                         'credit_card_refused' => $data['credit_card_refused'],
                                                                         'credit_card_paid'    => $data['credit_card_paid'],
                                                                         'abandoned_cart'      => $data['abandoned_cart'],
                                                                         'project_id'          => $data['project_id'],
                                                                         'user_id'             => auth()->user()->id,
                                                                     ]);
            if ($integrationCreated) {
                return response()->json([
                                            'message' => 'Integração criada com sucesso!',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Ocorreu um erro ao realizar a integração',
                                    ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao realizar integração  ConvertaXController - store');
            report($e);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('convertax::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        try {
            if (isset($id)) {
                $convertaxIntegrationModel = new ConvertaxIntegration();

                $userProjectModel = new UserProject();
                $projects         = [];

                $projectId    = current(Hashids::decode($id));
                $integration  = $convertaxIntegrationModel->where('project_id', $projectId)->first();
                $userProjects = $userProjectModel->where('user', auth()->user()->id)->with('project')->get();
                foreach ($userProjects as $userProject) {
                    $projects[] = $userProject->project;
                }

                if ($integration) {
                    return view('convertax::edit', ['projects' => $projects, 'integration' => $integration]);
                }
            }

            return response()->json([
                                        'message' => 'Erro',
                                    ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar Integração ConvertaX (ConvertaXController - edit)');
            report($e);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $convertaxIntegrationModel = new ConvertaxIntegration();

        $data          = $request->all();
        $data['value'] = preg_replace('/[.,]/', '', $data['value']);

        $integrationId        = current(Hashids::decode($id));
        $convertaxIntegration = $convertaxIntegrationModel->find($integrationId);
        if (empty($data['boleto_generated'])) {
            $data['boleto_generated'] = 0;
        }
        if (empty($data['boleto_paid'])) {
            $data['boleto_paid'] = 0;
        }
        if (empty($data['credit_card_paid'])) {
            $data['credit_card_paid'] = 0;
        }
        if (empty($data['credit_card_refused'])) {
            $data['credit_card_refused'] = 0;
        }
        if (empty($data['abandoned_cart'])) {
            $data['abandoned_cart'] = 0;
        }

        $integrationUpdated = $convertaxIntegration->update([
                                                                'link'                => $data['link'],
                                                                'value'               => $data['value'],
                                                                'boleto_generated'    => $data['boleto_generated'],
                                                                'boleto_paid'         => $data['boleto_paid'],
                                                                'credit_card_refused' => $data['credit_card_refused'],
                                                                'credit_card_paid'    => $data['credit_card_paid'],
                                                                'abandoned_cart'      => $data['abandoned_cart'],
                                                            ]);
        if ($integrationUpdated) {
            return response()->json([
                                        'message' => 'Integração atualizada com sucesso!',
                                    ], 200);
        }

        return response()->json([
                                    'message' => 'Ocorreu um erro ao atualizar a integração',
                                ], 400);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $projectId                 = current(Hashids::decode($id));
            $convertaxIntegrationModel = new ConvertaxIntegration();

            $integration        = $convertaxIntegrationModel->where('project_id', $projectId)->first();
            $integrationDeleted = $integration->delete();
            if ($integrationDeleted) {
                return response()->json([
                                            'message' => 'Integração Removida com sucesso!',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Erro ao tentar remover Integração',
                                    ], 400);
        } catch (Exception $e) {

        }
    }

    public function getIntegrations()
    {
        $convertaxIntegration = new ConvertaxIntegration();
        $projectModel         = new Project();
        $userProjectModel     = new UserProject();

        $projects              = [];
        $projectsIntegrated    = [];
        $userProjects          = $userProjectModel->where('user_id', auth()->user()->id)->with('project')->get();
        $convertaxIntegrations = $convertaxIntegration->where('user_id', auth()->user()->id)->get();

        foreach ($userProjects as $userProject) {
            $projects[] = $userProject->project;
        }

        foreach ($convertaxIntegrations as $convertaxIntegration) {
            $project = $projectModel->find($convertaxIntegration->project_id);
            if ($project) {
                $projectsIntegrated[] = $project;
            }
        }

        return view('convertax::include', ['projects' => $projects, 'projectsIntegrated' => $projectsIntegrated]);
    }
}
