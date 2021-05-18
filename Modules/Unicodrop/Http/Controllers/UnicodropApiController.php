<?php

namespace Modules\Unicodrop\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ReportanaIntegration;
use Modules\Core\Entities\UnicodropIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ProjectService;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Modules\Unicodrop\Transformers\UnicodropResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class UnicodropApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        try {
            $unicodropIntegrationModel = new UnicodropIntegration();
            $userProjectModel          = new UserProject();
            $projectModel              = new Project();

            activity()->on($unicodropIntegrationModel)->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos as integrações Unicodrop');

            $unicodropIntegrations = $unicodropIntegrationModel->where('user_id', auth()->user()->account_owner_id)
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
                                        'integrations' => UnicodropResource::collection($unicodropIntegrations),
                                        'projects'     => ProjectsSelectResource::collection($projects),
                                    ]);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('unicodrop::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $unicodropIntegrationModel = new UnicodropIntegration();
            $data                      = $request->all();
            $projectId                 = current(Hashids::decode($data['project_id']));
            if (!empty($projectId)) {
                $integration = $unicodropIntegrationModel->where('project_id', $projectId)->first();
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
                $integrationCreated = $unicodropIntegrationModel->create([
                                                                             'token'               => $data['project_id'],
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
            report($e);
            Log::warning('Erro ao realizar integração  UnicodropApiController - store');

            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        try {
            $unicodropIntegrationModel = new UnicodropIntegration();

            $unicodropIntegration = $unicodropIntegrationModel->find(current(Hashids::decode($id)));

            activity()->on($unicodropIntegrationModel)->tap(function(Activity $activity) use ($id) {
                $activity->log_name   = 'visualization';
                $activity->subject_id = current(Hashids::decode($id));
            })
                      ->log('Visualizou tela editar configurações de integração projeto ' . $unicodropIntegration->project->name . ' com Unicodrop');

            return new UnicodropResource($unicodropIntegration);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //        try {
        //            if (!empty($id)) {
        //                $unicodropIntegrationModel = new UnicodropIntegration();
        //
        //                $projectService = new ProjectService();
        //
        //                activity()->on($unicodropIntegrationModel)->tap(function(Activity $activity) use ($id) {
        //                    $activity->log_name   = 'visualization';
        //                    $activity->subject_id = current(Hashids::decode($id));
        //                })->log('Visualizou tela editar configurações da integração Unicodrop');
        //
        //                $projects = $projectService->getMyProjects();
        //
        //                $projectId   = current(Hashids::decode($id));
        //                $integration = $unicodropIntegrationModel->where('project_id', $projectId)->first();
        //
        //                if ($integration) {
        //                    return response()->json(['projects' => $projects, 'integration' => $integration]);
        //                } else {
        //                    return response()->json([
        //                                                'message' => 'Ocorreu um erro, tente novamente mais tarde!',
        //                                            ], 400);
        //                }
        //            } else {
        //
        //                return response()->json([
        //                                            'message' => 'Ocorreu um erro, tente novamente mais tarde!',
        //                                        ], 400);
        //            }
        //        } catch (Exception $e) {
        //            Log::warning('Erro ao tentar acessar tela editar Integração Unicodrop (UnicodropApiController - edit)');
        //            report($e);
        //
        //            return response()->json([
        //                                        'message' => 'Ocorreu um erro, tente novamente mais tarde!',
        //                                    ], 400);
        //        }
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

            $unicodropIntegrationModel = new UnicodropIntegration();
            $data                      = $request->all();
            $integrationId             = current(Hashids::decode($id));
            $reportanaIntegration      = $unicodropIntegrationModel->find($integrationId);
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
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $integrationId             = current(Hashids::decode($id));
            $unicodropIntegrationModel = new UnicodropIntegration();
            $integration               = $unicodropIntegrationModel->find($integrationId);
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
            Log::warning('Erro ao tentar remover Integração Unicodrop (UnicodropApiController - destroy)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao tentar remover, tente novamente mais tarde!',
                                    ], 400);
        }
    }
}
