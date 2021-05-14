<?php

namespace Modules\Reportana\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ReportanaIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\Project;
use Modules\Core\Services\ProjectService;
use Modules\Reportana\Transformers\ReportanaResource;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class ReportanaApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $reportanaIntegration = new ReportanaIntegration();
            $userProjectModel     = new UserProject();
            $projectModel         = new Project();

            activity()->on($reportanaIntegration)->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos as integrações Reportana');

            $reportanaIntegrations = $reportanaIntegration->where('user_id', auth()->user()->account_owner_id)
                                                          ->with('project')->get();

            $projects     = collect();
            $userProjects = $userProjectModel->where('user_id', auth()->user()->account_owner_id)->get();
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
                                        'integrations' => ReportanaResource::collection($reportanaIntegrations),
                                        'projects'     => ProjectsSelectResource::collection($projects)
                                    ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param $id
     * @return ReportanaResource
     */
    public function show($id)
    {
        try {
            $reportanaIntegrationModel = new ReportanaIntegration();
            $reportanaIntegration      = $reportanaIntegrationModel->find(current(Hashids::decode($id)));

            activity()->on($reportanaIntegrationModel)->tap(function(Activity $activity) use ($id) {
                $activity->log_name   = 'visualization';
                $activity->subject_id = current(Hashids::decode($id));
            })->log('Visualizou tela editar configurações de integração projeto ' . $reportanaIntegration->project->name . ' com Reportana');

            return new ReportanaResource($reportanaIntegration);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data                      = $request->all();
            $reportanaIntegrationModel = new ReportanaIntegration();

            $projectId = current(Hashids::decode($data['project_id']));
            if (!empty($projectId)) {
                $integration = $reportanaIntegrationModel->where('project_id', $projectId)->first();
                if ($integration) {
                    return response()->json([
                                                'message' => 'Projeto já integrado',
                                            ], 400);
                }
                if (empty($data['url_api'])) {
                    return response()->json(['message' => 'URl API é obrigatório!'], 400);
                }
                if (!filter_var($data['url_api'], FILTER_VALIDATE_URL)) {
                    return response()->json(['message' => 'URL API inválido!'], 400);
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

                $integrationCreated = $reportanaIntegrationModel->create([
                    'url_api'             => $data['url_api'],
                    'billet_generated'    => $data['boleto_generated'],
                    'billet_paid'         => $data['boleto_paid'],
                    'credit_card_refused' => $data['credit_card_refused'],
                    'credit_card_paid'    => $data['credit_card_paid'],
                    'abandoned_cart'      => $data['abandoned_cart'],
                    'project_id'          => $projectId,
                    'user_id'             => auth()->user()->account_owner_id,
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
            Log::warning('Erro ao realizar integração  ReportanaController - store');
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
                $reportanaIntegrationModel = new ReportanaIntegration();
                $projectService            = new ProjectService();

                activity()->on($reportanaIntegrationModel)->tap(function(Activity $activity) use ($id) {
                    $activity->log_name   = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })->log('Visualizou tela editar configurações da integração Reportana');

                $projects = $projectService->getMyProjects();

                $projectId   = current(Hashids::decode($id));
                $integration = $reportanaIntegrationModel->where('project_id', $projectId)->first();

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
            Log::warning('Erro ao tentar acessar tela editar Integração Reportana (ReportanaController - edit)');
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

        try {

            $reportanaIntegrationModel = new ReportanaIntegration();
            $data                      = $request->all();
            $integrationId             = current(Hashids::decode($id));
            $reportanaIntegration      = $reportanaIntegrationModel->find($integrationId);
            $messageError              = '';
            if (empty($data['url_api'])) {
                return response()->json(['message' => 'URl API é obrigatório!'], 400);
            }
            if (!filter_var($data['url_api'], FILTER_VALIDATE_URL)) {
                return response()->json(['message' => 'URL API inválido!'], 400);
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
            if (empty($data['abandoned_cart'])) {
                $data['abandoned_cart'] = 0;
            }

            $integrationUpdated = $reportanaIntegration->update([
                                                                    'url_api'             => $data['url_api'],
                                                                    'billet_generated'    => $data['boleto_generated'],
                                                                    'billet_paid'         => $data['boleto_paid'],
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
        } catch (Exception $e) {
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao atualizar a integração',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $integrationId             = current(Hashids::decode($id));
            $reportanaIntegrationModel = new ReportanaIntegration();
            $integration               = $reportanaIntegrationModel->find($integrationId);
            if (empty($integration)) {
                return response()->json([
                                            'message' => 'Erro ao tentar remover Integração',
                                        ], 400);
            } else {
                $integrationDeleted = $integration->delete();
                if ($integrationDeleted) {
                    return response()->json([
                                                'message' => 'Integração Removida com sucesso!',
                                            ], 200);
                }

                return response()->json([
                                            'message' => 'Erro ao tentar remover Integração',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar remover Integração Reportana (ReportanaController - destroy)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao tentar remover, tente novamente mais tarde!',
                                    ], 400);
        }
    }
}
