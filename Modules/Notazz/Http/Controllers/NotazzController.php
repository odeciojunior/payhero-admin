<?php

namespace Modules\Notazz\Http\Controllers;

use App\Entities\NotazzIntegration;
use App\Entities\Project;
use App\Entities\UserProject;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class NotazzController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('notazz::index');
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
            $userProjects     = $userProjectModel->where('user', auth()->user()->id)->with('projectId')->get();
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
        $data = $request->all();

        $notazzIntegrationModel = new NotazzIntegration();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        dd($id);
    }

    public function getIntegrations()
    {
        $notazzIntegrationModel = new NotazzIntegration();
        $projectModel           = new Project();
        $userProjectModel       = new UserProject();

        $projects           = [];
        $projectsIntegrated = [];
        $userProjects       = $userProjectModel->where('user', auth()->user()->id)->with('projectId')->get();
        $notazzIntegrations = $notazzIntegrationModel->where('user_id', auth()->user()->id)->get();

        foreach ($userProjects as $userProject) {
            $projects[] = $userProject->projectId;
        }

        foreach ($notazzIntegrations as $notazzIntegration) {
            $project = $projectModel->find($notazzIntegration->project_id);
            if ($project) {
                $projectsIntegrated[] = $project;
            }
        }

        return view('notazz::include', ['projects' => $projects, 'projectsIntegrated' => $projectsIntegrated]);
    }
}
