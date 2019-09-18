<?php

namespace Modules\ConvertaX\Http\Controllers;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Modules\Core\Entities\ConvertaxIntegration;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ProjectService;
use Vinkla\Hashids\Facades\Hashids;

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
     * @return Factory|JsonResponse|View
     */
    public function create()
    {
        try {
            $projectService = new ProjectService();

            $projects = $projectService->getMyProjects();

            if ($projects->count() > 0) {
                return view('convertax::create', ['projects' => $projects]);
            } else {

                return response()->json([
                                            'message' => 'Nenhum projeto encontrado',
                                        ], 200);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar redirecionar para tela de adicionar integração (ConvertaXController - create)');
            report($e);

            return response()->json([
                                        'message' => 'Nenhum projeto encontrado',
                                    ], 400);
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

            $projectId = current(Hashids::decode($data['project_id']));
            if (!empty($projectId)) {
                $integration = $convertaxIntegrationModel->where('project_id', $projectId)->first();
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

                $data['value'] = preg_replace('/[.,]/', '', $data['value']);

                $integrationCreated = $convertaxIntegrationModel->create([
                                                                             'link'                => $data['link'],
                                                                             'value'               => $data['value'],
                                                                             'boleto_generated'    => $data['boleto_generated'],
                                                                             'boleto_paid'         => $data['boleto_paid'],
                                                                             'credit_card_refused' => $data['credit_card_refused'],
                                                                             'credit_card_paid'    => $data['credit_card_paid'],
                                                                             'abandoned_cart'      => $data['abandoned_cart'],
                                                                             'project_id'          => $projectId,
                                                                             'user_id'             => auth()->user()->id,
                                                                         ]);
                if (!empty($integrationCreated)) {
                    return response()->json([
                                                'message' => 'Integração criada com sucesso!',
                                            ], 200);
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro ao realizar a integração',
                                            ], 400);
                }
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro ao realizar a integração',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao realizar integração  ConvertaXController - store');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao realizar a integração',
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
                $projectService            = new ProjectService();

                $projectId   = current(Hashids::decode($id));
                $integration = $convertaxIntegrationModel->where('project_id', $projectId)->first();

                $projects = $projectService->getMyProjects();

                if (!empty($integration)) {
                    return view('convertax::edit', ['projects' => $projects, 'integration' => $integration]);
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                            ], 400);
                }
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar Integração ConvertaX (ConvertaXController - edit)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 400);
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
        try {

            $convertaxIntegrationModel = new ConvertaxIntegration();

            $data          = $request->all();
            $data['value'] = preg_replace('/[.,]/', '', $data['value']);

            $integrationId        = current(Hashids::decode($id));
            $convertaxIntegration = $convertaxIntegrationModel->find($integrationId);
            if (!empty($convertaxIntegration)) {
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
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro ao atualizar a integração',
                                            ], 400);
                }
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro ao atualizar a integração',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar atualizar integração com convertaX (ConvertaXController - update)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao atualizar a integração',
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
