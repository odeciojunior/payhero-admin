<?php

namespace Modules\ProjectNotification\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Activitylog\Models\Activity;
use Modules\Core\Entities\ProjectNotification;
use Modules\Core\Entities\Project;
use Modules\Core\Services\ProjectNotificationService;
use Modules\ProjectNotification\Transformers\ProjectNotificationResource;

class ProjectNotificationApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index($projectId)
    {
        try {
            $projectNotificationModel   = new ProjectNotification();
            $projectNotificationService = new ProjectNotificationService();

            $projectId = current(Hashids::decode($projectId));
            // $projectNotificationService->createProjectNotificationDefault($projectId);

            activity()->on($projectNotificationModel)->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela de notificaçoes do projeto');

            $userId = auth()->user()->id;

            $projectNotifications = $projectNotificationModel->where('project_id', $projectId)
                                                             ->whereHas('userProject', function($q) use($userId) {
                                                                $q->where('user_id', $userId);
                                                             })
                                                             ->paginate(5);

            return ProjectNotificationResource::collection($projectNotifications);
        }
        catch(Exception $e){
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param $id
     */
    public function show($id)
    {
        
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

                activity()->on($projectNotificationModel)->tap(function(Activity $activity) use ($notificationId) {
                    $activity->log_name   = 'visualization';
                    $activity->subject_id = current(Hashids::decode($notificationId));
                })->log('Visualizou tela editar notificação do projeto');

                $notificationId   = current(Hashids::decode($notificationId));
                $notification = $projectNotificationModel->where('id', $notificationId)->first();

                // if (Gate::denies('edit', [$notification])) {
                //     return response()->json([
                //             'message' => 'Sem permissão',
                //         ],Response::HTTP_FORBIDDEN
                //     );
                // }

                if ($notification) {
                    return response()->json(['notification' => $notification]);
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
                $projectNotificationId = current(Hashids::decode($id));

                $project = $projectModel->find(current(Hashids::decode($projectId)));

                if (Gate::allows('edit', [$project])) {
                    $data = [];
                    if(!empty($request->input('message'))) {
                        $data['message'] = $request->input('message');
                    }
                    if(!is_null($request->input('status'))) {
                        $data['status'] = $request->input('status');
                    }

                    if(count($data) > 0) {
                        $notificationUpdated = $projectNotificationModel->where('id', $projectNotificationId)
                                                                        ->update($data);

                        if ($notificationUpdated) {
                            return response()->json('Sucesso', 200);
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
