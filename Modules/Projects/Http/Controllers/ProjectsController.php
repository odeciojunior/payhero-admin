<?php

namespace Modules\Projects\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Shipping;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\Facades\Image;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ProjectService;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Projects\Http\Requests\ProjectStoreRequest;
use Modules\Projects\Http\Requests\ProjectUpdateRequest;

class ProjectsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $projectModel = new Project();

            $projects = $projectModel->whereHas('usersProjects', function($query) {
                $query->where('user_id', auth()->user()->id);
            })->get();

            return view('projects::index', [
                'projects' => $projects,
            ]);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar pagina de projetos (ProjectsController - index)');
            report($e);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        try {
            $user = auth()->user()->load('companies');

            return view('projects::create', ['companies' => $user->companies]);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar pagina de criar Projeto (ProjectController - create)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProjectStoreRequest $request)
    {
        try {
            $requestValidated = $request->validated();

            $projectModel        = new Project();
            $userProjectModel    = new UserProject();
            $shippingModel       = new Shipping();
            $digitalOceanService = app(DigitalOceanFileService::class);

            if ($requestValidated) {
                $requestValidated['company'] = current(Hashids::decode($requestValidated['company']));

                $project = $projectModel->create([
                                                     'name'                       => $requestValidated['name'],
                                                     'description'                => $requestValidated['description'],
                                                     'installments_amount'        => 12,
                                                     'installments_interest_free' => 1,
                                                     'visibility'                 => 'private',
                                                     'automatic_affiliation'      => 0,
                                                     'boleto'                     => 1,
                                                 ]);
                $shippingModel->create([
                                           'project'      => $project->id,
                                           'name'         => 'Frete gratis',
                                           'information'  => 'de 15 até 30 dias',
                                           'value'        => '0,00',
                                           'type'         => 'static',
                                           'status'       => '1',
                                           'pre_selected' => '1',
                                       ]);
                if ($project) {
                    $photo = $request->file('photo-main');
                    if ($photo != null) {
                        try {
                            $img = Image::make($photo->getPathname());
                            $img->crop($requestValidated['photo_w'], $requestValidated['photo_h'], $requestValidated['photo_x1'], $requestValidated['photo_y1']);
                            $img->save($photo->getPathname());

                            $digitalOceanPath = $digitalOceanService
                                ->uploadFile("uploads/user/" . Hashids::encode(auth()->user()->id) . '/public/projects/' . $project->id_code . '/main', $photo);
                            $project->update(['photo' => $digitalOceanPath]);
                        } catch (Exception $e) {
                            Log::warning('Erro ao tentar salvar foto projeto - ProjectsController - store');
                            report($e);
                        }
                    }

                    $userProject = $userProjectModel->create([
                                                                 'user'              => auth()->user()->id,
                                                                 'project'           => $project->id,
                                                                 'company'           => $requestValidated['company'],
                                                                 'type'              => 'producer',
                                                                 'access_permission' => 1,
                                                                 'edit_permission'   => 1,
                                                                 'status'            => 'active',
                                                             ]);
                    if (!$userProject) {
                        $digitalOceanPath->deleteFile($project->photo);
                        $project->delete();

                        return redirect()->back()->with('error', 'Erro ao tentar salvar projeto');
                    }

                    return redirect()->route('projects.index');
                }
            }

            return redirect()->back()->with('error', 'Erro ao tentar salvar projeto');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar salvar projeto - ProjectsController -store');
            report($e);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function edit($id)
    {
        try {
            $user = auth()->user()->load('companies');

            $projectModel = new Project();

            $idProject = current(Hashids::decode($id));
            $project   = $projectModel->with([
                                                 'usersProjects' => function($query) use ($user, $idProject) {
                                                     $query->where('user', $user->id)
                                                           ->where('project', $idProject)->first();
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProjectUpdateRequest $request, $id)
    {
        try {

            $requestValidated    = $request->validated();
            $projectModel        = new Project();
            $userProjectModel    = new UserProject();
            $digitalOceanService = app(DigitalOceanFileService::class);

            if ($requestValidated) {

                $project = $projectModel->where('id', Hashids::decode($id))->first();

                if (Gate::allows('update', [$project])) {

                    if ($requestValidated['installments_amount'] < $requestValidated['installments_interest_free']) {
                        $requestValidated['installments_interest_free'] = $requestValidated['installments_amount'];
                    }

                    $requestValidated['cookie_duration'] = 60;
                    $requestValidated['status']          = 1;
                    //                    $requestValidated['support_phone']   = preg_replace("/[^0-9]/", "", $requestValidated['support_phone']);

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
                                    ->uploadFile('uploads/user/' . auth()->user()->id_code . '/public/projects/' . $project->id_code . '/main', $projectPhoto);
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
                                    ->uploadFile('uploads/user/' . auth()->user()->id_code . '/public/projects/' . $project->id_code . '/logo', $projectLogo);

                                $project->update([
                                                     'logo' => $digitalOceanPathLogo,
                                                 ]);
                            }
                        } catch (Exception $e) {
                            Log::warning('ProjectController - update - Erro ao enviar foto');
                            report($e);
                        }

                        $userProject = $userProjectModel->where([
                                                                    ['user', auth()->user()->id],
                                                                    ['project', $project->id],
                                                                ])->first();

                        $requestValidated['company'] = current(Hashids::decode($requestValidated['company']));

                        if ($userProject->company != $requestValidated['company']) {
                            $userProject->update(['company' => $requestValidated['company']]);
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
     * @return \Illuminate\Http\JsonResponse
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
}
