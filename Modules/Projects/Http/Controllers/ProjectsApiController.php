<?php

namespace Modules\Projects\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Core\Services\ProjectService;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Projects\Http\Requests\ProjectStoreRequest;
use Modules\Projects\Http\Requests\ProjectUpdateRequest;
use Modules\Projects\Transformers\ProjectsResource;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Modules\Projects\Transformers\UserProjectResource;
use Modules\Shopify\Transformers\ShopifyIntegrationsResource;
use Vinkla\Hashids\Facades\Hashids;

class ProjectsApiController extends Controller
{

    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $projectService = new ProjectService();

            $projects = $projectService->getUserProjects();

            return response()->json(ProjectsResource::collection($projects));

        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar pagina de projetos (ProjectsController - index)');
            report($e);

            return response()->json(['message' => 'Erro ao tentar acessar projetos'], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function create()
    {
        try {
            $user = auth()->user()->load('companies');
            return response()->json(CompaniesSelectResource::collection($user->companies));
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar pagina de criar Projeto (ProjectController - create)');
            report($e);
            return response()->json(['message' => 'Erro ao carregar empresas'], 400);
        }
    }


    public function store(ProjectStoreRequest $request)
    {
        try {
            $requestValidated = $request->validated();

            $projectModel = new Project();
            $userProjectModel = new UserProject();
            $shippingModel = new Shipping();
            $digitalOceanService = app(DigitalOceanFileService::class);

            if (!empty($requestValidated)) {
                $requestValidated['company'] = Hashids::decode($requestValidated['company'])[0];

                $project = $projectModel->create([
                    'name' => $requestValidated['name'],
                    'description' => $requestValidated['description'],
                    'installments_amount' => 12,
                    'installments_interest_free' => 1,
                    'visibility' => 'private',
                    'automatic_affiliation' => 0,
                    'boleto' => 1,
                ]);
                if (!empty($project)) {
                    $shipping = $shippingModel->create([
                        'project_id' => $project->id,
                        'name' => 'Frete gratis',
                        'information' => 'de 15 até 30 dias',
                        'value' => '0,00',
                        'type' => 'static',
                        'status' => '1',
                        'pre_selected' => '1',
                    ]);

                    if (!empty($shipping)) {
                        $photo = $request->file('photo-main');
                        if ($photo != null) {
                            try {
                                $img = Image::make($photo->getPathname());
                                $img->crop($requestValidated['photo_w'], $requestValidated['photo_h'], $requestValidated['photo_x1'], $requestValidated['photo_y1']);
                                $img->save($photo->getPathname());

                                $digitalOceanPath = $digitalOceanService
                                    ->uploadFile("uploads/user/" . Hashids::encode(auth()->user()->id) . '/public/projects/' . Hashids::encode($project->id) . '/main', $photo);
                                $project->update(['photo' => $digitalOceanPath]);
                            } catch (Exception $e) {
                                Log::warning('Erro ao tentar salvar foto projeto - ProjectsController - store');
                                report($e);
                            }
                        }

                        $userProject = $userProjectModel->create([
                            'user_id' => auth()->user()->id,
                            'project_id' => $project->id,
                            'company_id' => $requestValidated['company'],
                            'type' => 'producer',
                            'access_permission' => 1,
                            'edit_permission' => 1,
                            'status' => 'active',
                        ]);
                        if (!empty($userProject)) {
                            return response()->json(['message', 'Projeto salvo com sucesso']);
                        } else {
                            $digitalOceanPath->deleteFile($project->photo);
                            $shipping->delete();
                            $project->delete();

                            return response()->json(['message', 'Erro ao tentar salvar projeto'], 400);
                        }
                    } else {
                        $project->delete();

                        return response()->json(['message', 'Erro ao tentar salvar projeto'], 400);
                    }
                } else {
                    return response()->json(['message', 'Erro ao tentar salvar projeto'], 400);
                }
            } else {
                return response()->json(['message', 'Erro ao tentar salvar projeto'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar salvar projeto - ProjectsController -store');
            report($e);
            return response()->json(['message', 'Erro ao tentar salvar projeto'], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        try {
            if (isset($id)) {
                $projectModel = new Project();
                $userProjectModel = new UserProject();
                $shopifyIntegrationModel = new ShopifyIntegration();

                $user = auth()->user()->load('companies');

                $idProject = Hashids::decode($id)[0];
                $project = $projectModel->find($idProject);

                $userProject = $userProjectModel->where('user_id', $user->id)
                    ->where('project_id', $idProject)->first();
                $userProject = new UserProjectResource($userProject);

                $shopifyIntegrations = $shopifyIntegrationModel->where('user_id', $user->id)
                    ->where('project_id', $idProject)->get();
                $shopifyIntegrations = ShopifyIntegrationsResource::collection($shopifyIntegrations);

                $companies = CompaniesSelectResource::collection($user->companies);

                if (Gate::allows('edit', [$project])) {
                    $project = new ProjectsResource($project);
                    return response()->json(compact('companies', 'project', 'userProject', 'shopifyIntegrations'));
                } else {
                    return response()->json(['message' => 'Erro ao carregar configuraçoes do projeto'], 400);
                }
            }
            return response()->json([
                'message' => 'Erro ao carregar configuracoes do projeto',
            ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao carregar configuracoes do projeto (ProjectsApiController - edit)');
            report($e);
            return response()->json([
                'message' => 'Erro ao carregar configuracoes do projeto',
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
            $projectModel = new Project();

            $projectId = Hashids::decode($id)[0];

            $project = $projectModel->where('id', $projectId)->first();

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

    /**
     * @param ProjectUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ProjectUpdateRequest $request, $id)
    {
        try {

            $requestValidated = $request->validated();
            $projectModel = new Project();
            $userProjectModel = new UserProject();
            $digitalOceanService = app(DigitalOceanFileService::class);

            if ($requestValidated) {

                $project = $projectModel->find(Hashids::decode($id)[0]);

                if (Gate::allows('update', [$project])) {

                    if ($requestValidated['installments_amount'] < $requestValidated['installments_interest_free']) {
                        $requestValidated['installments_interest_free'] = $requestValidated['installments_amount'];
                    }

                    $requestValidated['cookie_duration'] = 60;
                    $requestValidated['status'] = 1;

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

                                $img->resize(null, 300, function ($constraint) {
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
                            return response()->json(['message', 'Erro ao atualizar projeto'], 400);
                        }

                        $userProject = $userProjectModel->where([
                            ['user_id', auth()->user()->id],
                            ['project_id', $project->id],
                        ])->first();
                        $requestValidated['company_id'] = Hashids::decode($requestValidated['company_id'])[0];
                        if ($userProject->company_id != $requestValidated['company_id']) {
                            $userProject->update(['company_id' => $requestValidated['company_id']]);
                        }

                        return response()->json(['message' => 'Projeto atualizado!'], 200);
                    }
                    return response()->json(['message', 'Erro ao atualizar projeto'], 400);
                } else {
                    return response()->json(['message' => 'Sem permissão para atualizar o projeto'], 403);
                }
            }
            return response()->json(['message', 'Erro ao atualizar projeto'], 400);
        } catch (Exception $e) {
            Log::warning('ProjectController - update - Erro ao atualizar project');
            report($e);
            return response()->json(['message', 'Erro ao atualizar projeto'], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse|ProjectsResource
     */
    public function show($id)
    {
        try {
            if ($id) {

                $projectModel = new Project();

                $project = $projectModel->find(Hashids::decode($id)[0]);

                if (Gate::allows('show', [$project])) {
                    return new ProjectsResource($project);
                } else {
                    return response()->json(['message' => 'Erro ao exibir detalhes do projeto'], 400);
                }
            } else {
                return response()->json(['message' => 'Erro ao exibir detalhes do projeto'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar detalhes do projeto (ProjectsController - show)');
            report($e);
            return response()->json(['message' => 'Erro ao exibir detalhes do projeto'], 400);
        }
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getProjects()
    {

        $projectService = new ProjectService();

        return ProjectsSelectResource::collection($projectService->getUserProjects());
    }
}

