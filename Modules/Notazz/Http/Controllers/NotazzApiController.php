<?php

namespace Modules\Notazz\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Notazz\Transformers\NotazzResource;
use Vinkla\Hashids\Facades\Hashids;

class NotazzApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        try {
            $notazzIntegrationModel = new NotazzIntegration();

            $notazzIntegrations = $notazzIntegrationModel->with(['project', 'project.usersProjects'])
                                                         ->whereHas('project.usersProjects', function($query) {
                                                             $query->where('user_id', auth()->user()->id);
                                                         })->get();

            return NotazzResource::collection($notazzIntegrations);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar integraçeõs da Notazz (NotazzApiController - index)');
            report($e);

            return response()->json(['message' => 'Ocorreu um erro ao listar as integrações com a notazz'], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        try {
            $userProjectModel = new UserProject();

            $projects     = [];
            $userProjects = $userProjectModel->with('projectId')->where('user', auth()->user()->id)->get();
            if ($userProjects->count() > 0) {
                foreach ($userProjects as $userProject) {
                    $projects[] = $userProject->projectId;
                }

                return view('notazz::create', ['projects' => $projects]);
            } else {

                return response()->json([
                                            'message' => 'Nenhum projeto encontrado',
                                        ], 222);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar redirecionar para tela de adicionar integração (NotazzController - create)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        try {
            $data                   = $request->all();
            $notazzIntegrationModel = new NotazzIntegration();

            $integration = $notazzIntegrationModel->where('project_id', $data['project_id'])->first();
            if ($integration) {
                return response()->json([
                                            'message' => 'Projeto já integrado',
                                        ], 400);
            }
            $integrationCreated = $notazzIntegrationModel->create([
                                                                      'token_webhook'   => $data['token_webhook'],
                                                                      'token_api'       => $data['token_api'],
                                                                      'token_logistics' => $data['token_logistics'],
                                                                      'project_id'      => $data['project_id'],
                                                                      'user_id'         => auth()->user()->id,
                                                                  ]);
            if ($integrationCreated) {
                return response()->json([
                                            'message' => 'Integração criada com sucesso!',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Ocorreu um erro ao realizar a integração',
                                    ], 400);
        } catch
        (Exception $e) {
            Log::warning('Erro ao realizar integração  NotazzController - store');
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
        return view('notazz::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        try {
            if (!empty($id)) {
                $notazzIntegrationModel = new NotazzIntegration();

                $userProjectModel = new UserProject();
                $projects         = [];

                $projectId    = current(Hashids::decode($id));
                $integration  = $notazzIntegrationModel->where('project_id', $projectId)->first();
                $userProjects = $userProjectModel->where('user', auth()->user()->id)->with('projectId')->get();
                foreach ($userProjects as $userProject) {
                    $projects[] = $userProject->projectId;
                }

                if ($integration) {
                    return view('notazz::edit', ['projects' => $projects, 'integration' => $integration]);
                }
            }

            return response()->json([
                                        'message' => 'Erro',
                                    ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar Integração Notazz (NotazzController - edit)');
            report($e);
        }

        return view('notazz::edit');
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

            $notazzIntegrationModel = new NotazzIntegration();

            $integrationId = current(Hashids::decode($id));

            if ($integrationId) {
                //hash ok

                $dataRequest = $request->all();

                $integrationNotazz = $notazzIntegrationModel->find($integrationId);

                $integrationNotazz->update([
                                               'token_webhook'   => $dataRequest['token_webhook'],
                                               'token_api'       => $dataRequest['token_api'],
                                               'token_logistics' => $dataRequest['token_logistics'],
                                           ]);

                return response()->json([
                                            'message' => 'Integração atualizada com sucesso.',
                                        ], 200);
            } else {
                //hash error

                return response()->json([
                                            'message' => 'Integração não encontrada',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao atualizar integraçeõs da Notazz (NotazzController - update)');
            report($e);

            return response()->json([
                                        'message' => 'Integração não encontrada',
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
            $notazzIntegrationModel = new NotazzIntegration();

            $projectId = current(Hashids::decode($id));

            if ($projectId) {
                //hash ok

                $integration = $notazzIntegrationModel->where('project_id', $projectId)->first();
                if ($integration) {

                    $integrationDeleted = $integration->delete();
                    if ($integrationDeleted) {
                        //integracao removida

                        return response()->json([
                                                    'message' => 'Integração removida com sucesso.',
                                                ], 200);
                    } else {
                        //erro ao remover integracao
                        return response()->json([
                                                    'message' => 'Erro ao remover integração',
                                                ], 400);
                    }
                } else {
                    return response()->json([
                                                'message' => 'Integração não encontrada',
                                            ], 400);
                }
            } else {
                //hash error
                return response()->json([
                                            'message' => 'Integração não encontrada',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar integraçeõs da Notazz (NotazzController - getIntegrations)');
            report($e);

            return response()->json([
                                        'message' => 'Integração não encontrada',
                                    ], 400);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIntegrations()
    {
        try {
            $notazzIntegrationModel = new NotazzIntegration();

            $notazzIntegrations = $notazzIntegrationModel->with(['project', 'project.usersProjects'])
                                                         ->whereHas('project.usersProjects', function($query) {
                                                             $query->where('user', auth()->user()->id);
                                                         })->get();

            $integrations = $notazzIntegrations->toArray();

            return view('notazz::include', ['integrations' => $integrations]);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar integraçeõs da Notazz (NotazzController - getIntegrations)');
            report($e);

            return view('notazz::include', ['integrations' => $integrations]);
        }
    }
}
