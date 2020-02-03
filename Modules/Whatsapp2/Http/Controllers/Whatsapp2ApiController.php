<?php

namespace Modules\Whatsapp2\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Whatsapp2Integration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\Project;
use Modules\Core\Services\ProjectService;
use Modules\Whatsapp2\Transformers\Whatsapp2Resource;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class Whatsapp2ApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $whatsapp2Integration = new Whatsapp2Integration();
            $userProjectModel     = new UserProject();
            $projectModel         = new Project();

            activity()->on($whatsapp2Integration)->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos as integrações whatsapp 2.0');

            $whatsapp2Integrations = $whatsapp2Integration->where('user_id', auth()->user()->account_owner_id)
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
                                        'integrations'    => Whatsapp2Resource::collection($whatsapp2Integrations),
                                        'projects'        => ProjectsSelectResource::collection($projects),
                                        'token_whatsapp2' => Hashids::connection('whatsapp2_token')
                                                                    ->encode(auth()->user()->account_owner_id),
                                    ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param $id
     * @return Whatsapp2Resource
     */
    public function show($id)
    {
        try {
            $whatsapp2IntegrationModel = new Whatsapp2Integration();
            $whatsapp2Integration      = $whatsapp2IntegrationModel->find(current(Hashids::decode($id)));

            activity()->on($whatsapp2IntegrationModel)->tap(function(Activity $activity) use ($id) {
                $activity->log_name   = 'visualization';
                $activity->subject_id = current(Hashids::decode($id));
            })->log('Visualizou tela editar configurações de integração projeto ' . $whatsapp2Integration->project->name . ' com whatsapp 2.0');

            return new Whatsapp2Resource($whatsapp2Integration);
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
            $whatsapp2IntegrationModel = new Whatsapp2Integration();

            $projectId = current(Hashids::decode($data['project_id']));
            if (!empty($projectId)) {
                $integration = $whatsapp2IntegrationModel->where('project_id', $projectId)->first();
                if ($integration) {
                    return response()->json([
                                                'message' => 'Projeto já integrado',
                                            ], 400);
                }
                if (empty($data['url_checkout']) || empty($data['url_order'])) {
                    return response()->json(['message' => 'URl Checkout e URL pedido são obrigatórios!'], 400);
                }
                if (!filter_var($data['url_checkout'], FILTER_VALIDATE_URL)) {
                    return response()->json(['message' => 'URL Checkout inválido!'], 400);
                }
                if (!filter_var($data['url_order'], FILTER_VALIDATE_URL)) {
                    return response()->json(['message' => 'URL Pedido inválido!'], 400);
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

                $integrationCreated = $whatsapp2IntegrationModel->create([
                                                                             'api_token'           => Hashids::connection('whatsapp2_token')
                                                                                                             ->encode(auth()->user()->account_owner_id),
                                                                             'url_order'           => $data['url_order'],
                                                                             'url_checkout'        => $data['url_checkout'],
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
            Log::warning('Erro ao realizar integração  Whatsapp2Controller - store');
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
                $whatsapp2IntegrationModel = new Whatsapp2Integration();
                $projectService            = new ProjectService();

                activity()->on($whatsapp2IntegrationModel)->tap(function(Activity $activity) use ($id) {
                    $activity->log_name   = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })->log('Visualizou tela editar configurações da integração whatsapp 2.0');

                $projects = $projectService->getMyProjects();

                $projectId   = current(Hashids::decode($id));
                $integration = $whatsapp2IntegrationModel->where('project_id', $projectId)->first();

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
            Log::warning('Erro ao tentar acessar tela editar Integração Whatsapp 2.0 (Whatsapp2Controller - edit)');
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

            $whatsapp2IntegrationModel = new Whatsapp2Integration();
            $data                      = $request->all();
            $integrationId             = current(Hashids::decode($id));
            $whatsapp2Integration      = $whatsapp2IntegrationModel->find($integrationId);
            $messageError              = '';
            if (empty($data['url_checkout']) || empty($data['url_order'])) {
                return response()->json(['message' => 'URl Checkout e URL pedido são obrigatórios!'], 400);
            }
            if (!filter_var($data['url_checkout'], FILTER_VALIDATE_URL)) {
                return response()->json(['message' => 'URL Checkout inválido!'], 400);
            }
            if (!filter_var($data['url_order'], FILTER_VALIDATE_URL)) {
                return response()->json(['message' => 'URL Pedido inválido!'], 400);
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

            $integrationUpdated = $whatsapp2Integration->update([
                                                                    'url_order'           => $data['url_order'],
                                                                    'url_checkout'        => $data['url_checkout'],
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
            $whatsapp2IntegrationModel = new Whatsapp2Integration();
            $integration               = $whatsapp2IntegrationModel->find($integrationId);
            $integrationDeleted        = $integration->delete();
            if ($integrationDeleted) {
                return response()->json([
                                            'message' => 'Integração Removida com sucesso!',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Erro ao tentar remover Integração',
                                    ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar remover Integração Whatsapp 2.0 (Whatsapp2Controller - destroy)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao tentar remover, tente novamente mais tarde!',
                                    ], 400);
        }
    }
}
