<?php

namespace Modules\HotZapp\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\HotzappIntegration;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ProjectService;
use Modules\HotZapp\Transformers\HotZappResource;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Vinkla\Hashids\Facades\Hashids;

class HotZappApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $hotzappIntegration = new HotzappIntegration();
            $userProjectModel   = new UserProject();
            $projectModel       = new Project();

            $hotzappIntegrations = $hotzappIntegration->where('user_id', auth()->id())->with('project')->get();

            $projects     = collect();
            $userProjects = $userProjectModel->where('user_id', auth()->id())->get();
            if ($userProjects->count() > 0) {
                foreach ($userProjects as $userProject) {
                    $project = $userProject->project()->where('status', $projectModel->present()->getStatus('active'))
                                           ->first();
                    if (!empty($project)) {
                        $projects->add($userProject->project);
                    }
                }
            }

            return response()->json([
                                        'integrations' => HotZappResource::collection($hotzappIntegrations),
                                        'projects'     => ProjectsSelectResource::collection($projects),
                                    ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param $id
     * @return HotZappResource
     */
    public function show($id)
    {

        $hotzappIntegrationModel = new HotzappIntegration();
        $hotzappIntegration      = $hotzappIntegrationModel->find(current(Hashids::decode($id)));

        return new HotZappResource($hotzappIntegration);
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
                if (empty($data['abandoned_cart'])) {
                    $data['abandoned_cart'] = 0;
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
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        try {
            if (!empty($id)) {
                $hotzappIntegrationModel = new HotzappIntegration();
                $projectService          = new ProjectService();

                $projects = $projectService->getMyProjects();

                $projectId   = current(Hashids::decode($id));
                $integration = $hotzappIntegrationModel->where('project_id', $projectId)->first();

                if ($integration) {
                    return response()->json(['projects' => $projects, 'integration' => $integration]);
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
     * @param Request $request
     * @param $id
     * @return JsonResponse
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
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $integrationId           = current(Hashids::decode($id));
            $hotzappIntegrationModel = new HotzappIntegration();
            $integration             = $hotzappIntegrationModel->find($integrationId);
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
}
