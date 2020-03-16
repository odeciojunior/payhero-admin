<?php

namespace Modules\ProjectNotification\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Activitylog\Models\Activity;
use Modules\Core\Entities\ProjectNotification;
use Modules\Core\Entities\Project;
use Modules\ProjectNotification\Transformers\ProjectNotificationResource;

class ProjectNotificationApiController extends Controller
{
    /**
     * @param $projectId
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index($projectId)
    {
        try {
            $projectNotificationModel = new ProjectNotification();

            $projectId = current(Hashids::decode($projectId));

            activity()->on($projectNotificationModel)->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela de notificaçoes do projeto');

            $userId = auth()->user()->id;

            $projectNotifications = $projectNotificationModel->where('project_id', $projectId)
                                                             ->whereHas('userProject', function($q) use ($userId) {
                                                                 $q->where('user_id', $userId);
                                                             })
                                                             ->paginate(5);

            return ProjectNotificationResource::collection($projectNotifications);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param $projectId
     * @param $id
     * @return JsonResponse|ProjectNotificationResource
     */
    public function show($projectId, $id)
    {
        try {
            if (isset($projectId) && isset($id)) {
                $projectNotificationModel = new ProjectNotification();
                $projectModel             = new Project();
                $projectNotification      = $projectNotificationModel->find(current(Hashids::decode($id)));
                $project                  = $projectModel->find(current(Hashids::decode($projectId)));

                activity()->on($projectModel)->tap(function(Activity $activity) use ($projectNotification) {
                    $activity->log_name   = 'visualization';
                    $activity->subject_id = $projectNotification->id;
                })->log('Visualizou tela detalhes da notificação do projeto');

                $projectNotification->project_image   = $project->photo;
                $projectNotification->project_name    = $project->name;
                $projectNotification->project_contact = $project->contact;

                if (Gate::allows('edit', [$project])) {
                    if ($projectNotification) {
                        return new ProjectNotificationResource($projectNotification);
                    } else {
                        return response()->json(['message' => 'Erro ao buscar notificação'], 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para visualizar notificação'], 400);
                }
            }

            return response()->json(['message' => 'Erro ao buscar Notificação'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados da notificação (ProjectNotificationApiController - show)');
            report($e);

            return response()->json(['message' => 'Erro ao buscar Notificação'], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($projectId, $notificationId)
    {
        try {
            if (!empty($projectId)) {
                $projectNotificationModel = new ProjectNotification();
                // $projectModel             = new Project();

                // $project = $projectModel->find(current(Hashids::decode($notificationId)));

                activity()->on($projectNotificationModel)->tap(function(Activity $activity) use ($notificationId) {
                    $activity->log_name   = 'visualization';
                    $activity->subject_id = current(Hashids::decode($notificationId));
                })->log('Visualizou tela editar notificação do projeto');

                $notificationId = current(Hashids::decode($notificationId));
                $notification   = $projectNotificationModel->where('id', $notificationId)->first();

                // if (Gate::denies('edit', [$project])) {
                //     return response()->json([
                //             'message' => 'Sem permissão',
                //         ],Response::HTTP_FORBIDDEN
                //     );
                // }

                if ($notification) {
                    return new ProjectNotificationResource($notification);
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
            Log::warning('Erro ao tentar acessar tela editar notificação do projeto (ProjectNotificationApiController - edit)');
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
    public function update(Request $request, $projectId, $id)
    {
        try {
            if (isset($projectId) && isset($id)) {
                $projectNotificationModel = new ProjectNotification();
                $projectModel             = new Project();
                $projectNotificationId    = current(Hashids::decode($id));
                $projectNotification      = $projectNotificationModel->find($projectNotificationId);

                $project = $projectModel->find(current(Hashids::decode($projectId)));

                activity()->on($projectNotificationModel)
                          ->tap(function(Activity $activity) use ($projectNotificationId) {
                              $activity->log_name   = 'updated';
                              $activity->subject_id = $projectNotificationId;
                          })->log('Atualizou notificação do projeto');

                if (Gate::allows('edit', [$project])) {
                    $data = [];
                    if (!empty($request->input('message'))) {
                        if ($projectNotification->type_enum == 1) {
                            $data['message'] = json_encode([
                                                               'subject' => $request->input('subject'),
                                                               'title'   => $request->input('title'),
                                                               'content' => $request->input('message'),
                                                           ]);
                        } else {
                            $data['message'] = $request->input('message');
                        }
                    }
                    if (!is_null($request->input('status'))) {
                        $data['status'] = $request->input('status');
                    }

                    if (count($data) > 0) {
                        $notificationUpdated = $projectNotification->update($data);

                        if ($notificationUpdated) {
                            return response()->json(['message' => 'Atualizado com sucesso', 'status' => $data['status']], 200);
                        } else {
                            return response()->json(['message' => 'Erro ao atualizar notificação'], 400);
                        }
                    } else {
                        return response()->json(['message' => 'Dados inválidos para atualizar notificação'], 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para atualizar notificação'], 400);
                }
            }

            return response()->json(['message' => 'Erro ao atualizar notificação'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao atualizar notificação (ProjectNotificationApiController - update)');
            report($e);

            return response()->json(['message' => 'Erro ao atualizar notificação'], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {

    }
}
