<?php

namespace Modules\Projects\Http\Controllers;

use Exception;
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
use Modules\Projects\Transformers\UserProjectResource;
use Modules\Shopify\Transformers\ShopifyIntegrationsResource;
use Vinkla\Hashids\Facades\Hashids;

class ProjectsApiController extends Controller
{

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
                $requestValidated['company'] = current(Hashids::decode($requestValidated['company']));

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
                            return redirect()->route('projects.index')->with('success', 'Projeto salvo com sucesso!');
                        } else {
                            $digitalOceanPath->deleteFile($project->photo);
                            $shipping->delete();
                            $project->delete();

                            return redirect()->back()->with('error', 'Erro ao tentar salvar projeto');
                        }
                    } else {
                        $project->delete();

                        return redirect()->back()->with('error', 'Erro ao tentar salvar projeto');
                    }
                } else {
                    return redirect()->back()->with('error', 'Erro ao tentar salvar projeto');
                }
            } else {
                return redirect()->back()->with('error', 'Erro ao tentar salvar projeto');
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar salvar projeto - ProjectsController -store');
            report($e);

            return redirect()->back()->with('error', 'Erro ao tentar salvar projeto');
        }
    }

    public function edit($id)
    {
        try{
            if(isset($id)){
                $projectModel = new Project();
                $userProjectModel = new UserProject();
                $shopifyIntegrationModel = new ShopifyIntegration();

                $user = auth()->user()->load('companies');

                $idProject = Hashids::decode($id)[0];
                $project = $projectModel->find($idProject)->makeHidden(['id', 'carrier_id']);

                $userProject = $userProjectModel->where('user_id', $user->id)
                    ->where('project_id', $idProject)->first();
                $userProject = new UserProjectResource($userProject);

                $shopifyIntegrations = $shopifyIntegrationModel->where('user_id', $user->id)
                    ->where('project_id', $idProject)->get();
                $shopifyIntegrations = ShopifyIntegrationsResource::collection($shopifyIntegrations);

                $companies = CompaniesSelectResource::collection($user->companies);

                if (Gate::allows('edit', [$project])) {
                    return response()->json(compact('companies','project', 'userProject', 'shopifyIntegrations'));
                } else {
                    return response()->json(['message' => 'Erro ao carregar configuraçoes do projeto'], 400);
                }
            }
            return response()->json([
                'message' => 'Erro ao carregar configuracoes do projeto',
            ], 400);
        }catch (Exception $e){
            Log::warning('Erro ao carregar configuracoes do projeto (ProjectsApiController - edit)');
            report($e);
            return response()->json([
                'message' => 'Erro ao carregar configuracoes do projeto',
            ], 400);
        }
    }

    public function destroy($id){
        try {
            $projectModel = new Project();

            $projectId = Hashids::decode($id)[0];

            $project   = $projectModel->where('id', $projectId)->first();

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
                            return response()->json(['message', 'Erro ao atualizar projeto'], 400);
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


//    public function index()  {
//
//        $projetos_usuario = UserProjeto::where([
//            ['user',\Auth::user()->id],
//            ['tipo','produtor']
//        ])->pluck('projeto')->toArray();
//
//        $projetos = Projeto::whereIn('id',$projetos_usuario);
//
//        return ProjetosResource::collection($projetos_usuario->paginate());
//    }
//
//    public function store(Request $request)  {
//
//        $dados = $request->all();
//
//        $projeto = Projeto::create($dados);
//
//        UserProjeto::create([
//            'user'              => \Auth::user()->id,
//            'projeto'           => $projeto->id,
//            'empresa'           => $dados['empresa'],
//            'tipo'              => 'produtor',
//            'responsavel_frete' => true,
//            'permissao_acesso'  => true,
//            'permissao_editar'  => true,
//            'status'            => 'ativo'
//        ]);
//
//        return response()->json('sucesso');
//    }
//
//    public function show($id)  {
//
//        $projeto = Projeto::find(Hashids::decode($id));
//
//        if(!$projeto){
//            return response()->json('Projeto não encontrado');
//        }
//
//        if(!$this->isAuthorized($projeto['id'])){
//            return response()->json('não autorizado');
//        }
//
//        $projeto_usuario = UserProjeto::where([
//            ['user',\Auth::user()->id],
//            ['tipo','produtor'],
//            ['projeto', $projeto['id']]
//        ])->first();
//
//        if(!$projeto_usuario){
//            return response()->json('Sem autorização');
//        }
//
//        return response()->json($projeto);
//    }
//
//    public function update(Request $request)  {
//
//        $projeto = Projeto::find(Hashids::decode($dados['id']));
//
//        if(!$projeto){
//            return response()->json('projeto não encontrado');
//        }
//
//        if(!$this->isAuthorized($projeto['id'])){
//            return response()->json('não autorizado');
//        }
//
//        $projeto_usuario = UserProjeto::where([
//            ['user',\Auth::user()->id],
//            ['tipo','produtor'],
//            ['projeto', $projeto['id']]
//        ])->first();
//
//        if(!$projeto_usuario){
//            return response()->json('Sem autorização');
//        }
//
//        $projeto->update($dados);
//
//        return response()->json('sucesso');
//    }
//
//    public function destroy($id)  {
//
//        $projeto = Projeto::find(Hashids::decode($id));
//
//        if(!$projeto){
//            return response()->json('projeto não encontrado');
//        }
//
//        $projeto_usuario = UserProjeto::where([
//            ['user',\Auth::user()->id],
//            ['tipo','produtor'],
//            ['projeto', $projeto['id']]
//        ])->first();
//
//        if(!$projeto_usuario){
//            return response()->json('Sem autorização');
//        }
//
//        $projeto->delete();
//
//        return response()->json('sucesso');
//    }
//
//    public function isAuthorized($id_projeto){
//
//        $projeto_usuario = UserProjeto::where([
//            ['user',\Auth::user()->id],
//            ['tipo','produtor'],
//            ['projeto', $id_projeto]
//        ])->first();
//
//        if(!$projeto_usuario){
//            return false;
//        }
//
//        return true;
//    }

}

