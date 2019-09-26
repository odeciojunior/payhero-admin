<?php

namespace Modules\Projects\Http\Controllers;

use Exception;
use Throwable;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Shipping;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Facades\Image;
use Illuminate\Contracts\View\Factory;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ProjectService;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Projects\Http\Requests\ProjectStoreRequest;
use Modules\Projects\Http\Requests\ProjectUpdateRequest;
use Modules\Projects\Transformers\ProjectsSelectResource;

class ProjectsController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('projects::index');
    }

    /**
     * @return Factory|View
     */
    public function create()
    {
        return view('projects::create');
    }

    /**
     * @param $id
     * @return Factory|View
     */
    public function show($id)
    {
        try {
            if ($id) {
                $idProject = current(Hashids::decode($id));

                $projectModel = new Project();

                $user      = auth()->user()->load('companies');
                $companies = $user->companies;

                $project = $projectModel->with(['usersProjects', 'shopifyIntegrations'])->where('id', $idProject)
                                        ->first();

                if (Gate::allows('show', [$project])) {
                    return view('projects::project', ['project' => $project, 'companies' => $companies]);
                } else {
                    return redirect()->route('projects.index');
                }
            } else {
                return redirect()->route('projects.index');
            }
        } catch (Exception $e) {

            Log::warning('Erro ao tentar acessar detalhes do projeto (ProjectsController - show)');
            report($e);

            return redirect()->route('projects.index');
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id)
    {
        try {
            $user = auth()->user()->load('companies');

            $projectModel = new Project();

            $idProject = current(Hashids::decode($id));
            $project   = $projectModel->with([
                                                 'usersProjects' => function($query) use ($user, $idProject) {
                                                     $query->where('user_id', $user->id)
                                                           ->where('project_id', $idProject)->first();
                                                 },
                                             ])->find($idProject);
            if (Gate::allows('edit', [$project])) {
                $view = view('projects::edit', [
                    'companies' => $user->companies,
                    'project'   => $project,
                ]);

                return response()->json($view->render());
            } else {
                return redirect()->route('projects.index');
            }
        } catch (Exception $e) {
            Log::error('Erro ao tentar buscar dados do edit (ProjectController - edit)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ProjectUpdateRequest $request, $id)
    {
        try {

            $requestValidated    = $request->validated();
            $projectModel        = new Project();
            $userProjectModel    = new UserProject();
            $digitalOceanService = app(DigitalOceanFileService::class);

            if ($requestValidated) {

                $project = $projectModel->find(current(Hashids::decode($id)));

                if (Gate::allows('update', [$project])) {

                    if ($requestValidated['installments_amount'] < $requestValidated['installments_interest_free']) {
                        $requestValidated['installments_interest_free'] = $requestValidated['installments_amount'];
                    }

                    $requestValidated['cookie_duration'] = 60;
                    $requestValidated['status']          = 1;

                    $projectUpdate = $project->update($requestValidated);
                    if ($projectUpdate) {
                        try {
                            $projectPhoto = $request->file('photo');
                            if ($projectPhoto != null) {
                                $digitalOceanService->deleteFile($project->photo);
                                $img = Image::make($projectPhoto->getPathname());
                                $img->crop($requestValidated['photo_w'], $requestValidated['photo_h'], $requestValidated['photo_x1'], $requestValidated['photo_y1']);
                                $img->resize(300, 300);
                                $img->save($projectPhoto->getPathname());

                                $digitalOceanPath = $digitalOceanService
                                    ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->id) . '/public/projects/' . Hashids::encode($project->id) . '/main', $projectPhoto);
                                $project->update([
                                                     'photo' => $digitalOceanPath,
                                                 ]);
                            }

                            $projectLogo = $request->file('logo');
                            if ($projectLogo != null) {

                                $digitalOceanService->deleteFile($project->logo);
                                $img = Image::make($projectLogo->getPathname());

                                $img->resize(null, 300, function($constraint) {
                                    $constraint->aspectRatio();
                                });

                                $img->save($projectLogo->getPathname());

                                $digitalOceanPathLogo = $digitalOceanService
                                    ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->id) . '/public/projects/' . Hashids::encode($project->id) . '/logo', $projectLogo);

                                $project->update([
                                                     'logo' => $digitalOceanPathLogo,
                                                 ]);
                            }
                        } catch (Exception $e) {
                            Log::warning('ProjectController - update - Erro ao enviar foto');
                            report($e);
                        }

                        $userProject                    = $userProjectModel->where([
                                                                                       ['user_id', auth()->user()->id],
                                                                                       ['project_id', $project->id],
                                                                                   ])->first();
                        $requestValidated['company_id'] = current(Hashids::decode($requestValidated['company_id']));
                        if ($userProject->company_id != $requestValidated['company_id']) {
                            $userProject->update(['company_id' => $requestValidated['company_id']]);
                        }

                        return response()->json(['message' => 'Projeto atualizado!'], 200);
                    }

                    return response()->json('error', 422);
                } else {
                    return response()->json(['message' => 'Sem permissão para atualizar o projeto'], 403);
                }
            }
        } catch (Exception $e) {
            Log::warning('ProjectController - update - Erro ao atualizar project');
            report($e);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $projectModel = new Project();

            $projectId = current(Hashids::decode($id));
            $project   = $projectModel->where('id', Hashids::decode($id))->first();

            if (Gate::allows('destroy', [$project])) {

                $projectService = new ProjectService();

                if ($projectId) {
                    if (!$projectService->hasSales($projectId)) {
                        //n tem venda
                        if ($projectService->delete($projectId)) {
                            //projeto removido
                            return response()->json('success', 200);
                        } else {
                            //erro ao remover projeto
                            return response()->json('error', 400);
                        }
                    } else {
                        return response()->json('Impossível remover projeto, possui vendas', 400);
                    }
                } else {
                    return response()->json('Projeto não encontrado', 400);
                }
            } else {
                return response()->json('Sem permissão para remover projeto', 403);
            }
        } catch (Exception $e) {
            Log::warning('ProjectController - delete - Erro ao deletar project');
            report($e);

            return response()->json('Erro ao remover o projeto, tente novamente mais tarde', 400);
        }
    }

    public function getProjects(){

        $projectService = new ProjectService();

        return ProjectsSelectResource::collection($projectService->getUserProjects());
    }
}
