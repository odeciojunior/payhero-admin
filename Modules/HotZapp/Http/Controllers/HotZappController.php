<?php

namespace Modules\HotZapp\Http\Controllers;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\HotzappIntegration;

/**
 * Class HotZappController
 * @package Modules\HotZapp\Http\Controllers
 */
class HotZappController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('hotzapp::index');
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
                    if (!empty($userProject->project)) {
                        $projects[] = $userProject->project;
                    }
                }

                return view('hotzapp::create', ['projects' => $projects]);
            } else {

                return response()->json([
                                            'message' => 'Nenhum projeto encontrado',
                                        ], 222);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar redirecionar para tela de adicionar integração (HotZappController - create)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 222);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data                    = $request->all();
            $hotzappIntegrationModel = new HotzappIntegration();

            $projectId = current(Hashids::decode($data['project_id']));
            if (!empty($projectId)) {
                $integration = $hotzappIntegrationModel->where('project_id', $projectId)->first();
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

                $integrationCreated = $hotzappIntegrationModel->create([
                                                                           'link'                => $data['link'],
                                                                           'boleto_generated'    => $data['boleto_generated'],
                                                                           'boleto_paid'         => $data['boleto_paid'],
                                                                           'credit_card_refused' => $data['credit_card_refused'],
                                                                           'credit_card_paid'    => $data['credit_card_paid'],
                                                                           'abandoned_cart'      => $data['abandoned_cart'],
                                                                           'project_id'          => $projectId,
                                                                           'user_id'             => auth()->user()->id,
                                                                       ]);

                if ($integrationCreated) {
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
            Log::warning('Erro ao realizar integração  HotZappController - store');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao realizar a integração',
                                    ], 400);
        }
    }

    /**
     * @return Factory|View
     */
    public function show()
    {
        return view('hotzapp::show');
    }

    /**
     * @param $id
     * @return Factory|JsonResponse|View
     */
    public function edit($id)
    {
        try {
            if (!empty($id)) {
                $hotzappIntegrationModel = new HotzappIntegration();
                $projectModel            = new Project();

                $projectId   = current(Hashids::decode($id));
                $integration = $hotzappIntegrationModel->where('project_id', $projectId)->first();
                $projects    = $projectModel->present()->getProjects();

                if ($integration) {
                    return view('hotzapp::edit', ['projects' => $projects, 'integration' => $integration]);
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                            ], 400);
                }
            } else {

                return response()->json([
                                            'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar Integração HotZapp (HotZappController - edit)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde!',
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
        $hotzappIntegrationModel = new HotzappIntegration();
        $data                    = $request->all();
        $integrationId           = current(Hashids::decode($id));
        $hotzappIntegration      = $hotzappIntegrationModel->find($integrationId);
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

        $integrationUpdated = $hotzappIntegration->update([
                                                              'link'                => $data['link'],
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
            $projectId               = current(Hashids::decode($id));
            $hotzappIntegrationModel = new HotzappIntegration();
            $integration             = $hotzappIntegrationModel->where('project_id', $projectId)->first();
            $integrationDeleted      = $integration->delete();
            if ($integrationDeleted) {
                return response()->json([
                                            'message' => 'Integração Removida com sucesso!',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Erro ao tentar remover Integração',
                                    ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar remover Integração HotZapp (HotZappController - destroy)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao tentar remover, tente novamente mais tarde!',
                                    ], 400);
        }
    }

    /**
     * @return Factory|View
     */
    public function getIntegrations()
    {
        $hotzappIntegrationModel = new HotzappIntegration();
        $projectModel            = new Project();

        $projectsIntegrated  = [];
        $hotzappIntegrations = $hotzappIntegrationModel->where('user_id', auth()->user()->id)->get();

        foreach ($hotzappIntegrations as $hotzappIntegration) {
            $project = $projectModel->find($hotzappIntegration->project_id);
            if ($project) {
                $projectsIntegrated[] = $project;
            }
        }

        return view('hotzapp::include', ['projectsIntegrated' => $projectsIntegrated]);
    }
}
