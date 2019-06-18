<?php

namespace Modules\Projects\Http\Controllers;

use Exception;
use App\Entities\Project;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use App\Entities\ExtraMaterial;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Helpers\CaminhoArquivosHelper;
use Modules\Core\Services\DigitalOceanFileService;

class ProjectsController extends Controller
{
    private $projectModel;
    private $userProjectModel;
    private $extraMaterialsModel;
    private $digitalOceanFileService;
 
    function getProject()
    {
        if (!$this->projectModel) {
            $this->projectModel = app(Project::class);
        }

        return $this->projectModel;
    }

    private function getUserProject()
    {
        if (!$this->userProjectModel) {
            $this->userProjectModel = app(UserProject::class);
        }

        return $this->userProjectModel;
    }

    public function getExtraMaterials()
    {
        if (!$this->extraMaterialsModel) {
            $this->extraMaterialsModel = app(ExtraMaterial::class);
        }

        return $this->extraMaterialsModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDigitalOceanFileService()
    {
        if (!$this->digitalOceanFileService) {
            $this->digitalOceanFileService = app(DigitalOceanFileService::class);
        }

        return $this->digitalOceanFileService;
    }

    public function index()
    {
        try {
            $projects = $this->getProject()->whereHas('usersProjects', function($query) {
                $query->where('user', auth()->user()->id);
            })->get();

            return view('projects::index', ['projects' => $projects]);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar pagina de projetos (ProjectsController - index)');
            report($e);
        }
    }

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

    public function edit($id)
    {
        try {
            $user    = auth()->user()->load('companies');
            $project = $this->getProject()->with([
                                                     'usersProjects' => function($query) use ($user) {
                                                         $query->where('user', $user->id)->first();
                                                     },
                                                     'usersProjects.company',
                                                     'shippings',
                                                     'extraMaterials',

                                                 ])->where('id', Hashids::decode($id))->first();

            $view = view('projects::edit', [
                'project'        => $project,
                'companies'      => $user->companies,
                'extraMaterials' => $project->extraMaterials,
                'emp'            => $user->company,
                'shippings'      => $project->shippings,

            ]);

            return response()->json($view->render());
        } catch (Exception $e) {
            Log::error('Erro ao tentar buscar dados do edit (ProjectController - edit)');
            report($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $data      = $request->all();
            $companyId = Hashids::decode($data['company']);

            $project = $this->getProject()->create($data);

            $userProject = $this->getUserProject()->create([
                'user'              => auth()->user()->id,
                'project'           => $project->id,
                'company'           => $companyId[0],
                'type'              => 'producer',
                'access_permission' => 1,
                'edit_permission'   => 1,
                'status'            => 'active',
            ]);

            $projectPhoto = $request->file('project_photo');

            if ($projectPhoto != null) {

                try {
                    $img = Image::make($projectPhoto->getPathname());
                    $img->crop($data['photo_w'], $data['photo_h'], $data['photo_x1'], $data['photo_y1']);
                    $img->resize(200, 200);
                    $img->save($projectPhoto->getPathname());

                    $digitalOceanPath = $this->getDigitalOceanFileService()
                                             ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->id) . '/public/projects', $projectPhoto);

                    $project->update([
                        'photo' => $digitalOceanPath,
                    ]); 

                } catch (Exception $e) {
                    dd($e);
                    Log::warning('ProjectController - store - Erro ao enviar foto do project');
                    report($e);
                }
            }

            return redirect()->route('projects.index');
        } catch (Exception $e) {
            dd($e);
            Log::error('Erro ao tentar salvar projeto (ProjectsController - store)' . $e->getFile());
            report($e);
        }
    }

    public function update($id, Request $request)
    {
        try{
            $dataRequest = $request->all();

            $project = Project::where('id', Hashids::decode($id))->first();

            $project->update($dataRequest);

            $projectPhoto = $request->file('project_photo');

            if ($projectPhoto != null) {

                try {
                    $this->getDigitalOceanFileService()->deleteFile($project->photo);

                    $img = Image::make($projectPhoto->getPathname());
                    $img->crop($dataRequest['project_photo_w'], $dataRequest['project_photo_h'], $dataRequest['project_photo_x1'], $dataRequest['project_photo_y1']);
                    $img->resize(200, 200);
                    $img->save($projectPhoto->getPathname());

                    $digitalOceanPath = $this->getDigitalOceanFileService()
                                            ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->id) . '/public/projects', $projectPhoto);

                    $project->update([
                        'photo' => $digitalOceanPath,
                    ]);

                } catch (Exception $e) {
                    dd($e);
                    Log::warning('ProjectController - update - Erro ao atualizar foto do project');
                    report($e);
                }
            }

            $userProject = UserProject::where([
                ['user', \Auth::user()->id],
                ['project', $project['id']],
            ])->first();

            if ($userProject->company != $dataRequest['company']) {
                $userProject->company = $dataRequest['company'];
                $userProject->update();
            }

            return response()->json('sucesso');
        }
        catch(\Exception $e){
            Log::warning('ProjectController - update - Erro ao atualizar project');
            report($e);
        }
    }

    public function delete(Request $request)
    {
        try{
            $dataRequest = $request->all();

            $project = Project::where('id', Hashids::decode($dataRequest['projeto']))->first();

            $plans = Plan::where('project', $project->id)->pluck('id')->toArray();

            $productsPlans = ProductPlan::whereIn('plan', $plans)->pluck('product')->toArray();

            $this->getDigitalOceanFileService()->deleteFile($project->photo);

            // $project->delete();

            return response()->json('sucesso');
        }
        catch(\Exception $e){
            Log::warning('ProjectController - delete - Erro ao deletar project');
            report($e);
        }
    }

    public function show($id)
    {
        try {
            if ($id) {
                $project = $this->getProject()->where('id', Hashids::decode($id))->first();

                return view('projects::project', ['project' => $project]);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar detalhes do projeto (ProjectsController - show)');
            report($e);
        }
    }

    public function getDadosProject($id)
    {

        $project   = Project::find(Hashids::decode($id)[0]);
        $idProject = Hashids::encode($project->id);

        $userProject = UserProject::where([
                                              ['project', $project['id']],
                                              ['tipo', 'produtor'],
                                          ])->first();

        $usuario = User::find($userProject['user']);
        $planos  = Plano::where('project', $project['id'])->get()->toArray();

        foreach ($planos as &$plano) {
            $plano['lucro'] = number_format($plano['preco'] * $project['porcentagem_afiliados'] / 100, 2);
        }

        $view = view('projects::detalhes', [
            'id_project' => $idProject,
            'project'    => $project,
            'planos'     => $planos,
            'produtor'   => $usuario['name'],
            'empresa'    => $produtor->empresa,
        ]);

        return response()->json($view->render());
    }

    public function addMaterialExtra(Request $request)
    {

        $dataRequest = $request->all();

        $dataRequest['descricao'] = $dataRequest['descricao_material_extra'];

        if ($dataRequest['tipo'] == 'video') {
            $dataRequest['material'] = $dataRequest['material_extra_video'];
            MaterialExtra::create($dataRequest);
        } else if ($dataRequest['tipo'] == 'imagem') {

            $materialExtra = MaterialExtra::create($dataRequest);

            $imagem = $request->file('material_extra_imagem');

            if ($imagem != null) {
                $nomeFoto = 'foto_' . $materialExtra->id . '_.' . $imagem->getClientOriginalExtension();

                Storage::delete('public/upload/materialextra/fotos/' . $nomeFoto);

                $imagem->move(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJEct_FOTO, $nomeFoto);

                $img = Image::make(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJEct_FOTO . $nomeFoto);

                Storage::delete('public/upload/materialextra/fotos/' . $nomeFoto);

                $img->save(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJEct_FOTO . $nomeFoto);

                $materialExtra->update([
                                           'material' => $nomeFoto,
                                       ]);
            }
        } else if ($dataRequest['tipo'] == 'pdf') {

            $materialExtra = MaterialExtra::create($dataRequest);

            $arquivo = $request->file('material_extra_pdf');

            if ($arquivo != null) {
                $nome_pdf = 'pdf_' . $materialExtra->id . '_.' . $arquivo->getClientOriginalExtension();

                Storage::delete('public/upload/materialextra/pdfs/' . $nome_pdf);

                $arquivo->move(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJEct_FOTO, $nome_pdf);

                $img = Image::make(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJEct_FOTO . $nome_pdf);

                Storage::delete('public/upload/materialextra/pdfs/' . $nome_pdf);

                $img->save(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJEct_FOTO . $nome_pdf);

                $materialExtra->update([
                                           'material' => $nome_pdf,
                                       ]);
            }
        }

        return response()->json('sucesso');
    }

    public function deleteExtraMaterial(Request $request)
    {
        try {
            $data = $request->all();

            $extraMaterial = $this->getExtraMaterials()->find($data['idMaterialExtra']);

            if (!$extraMaterial) {
                return response()->json('erro');
            }

            $extraMaterialDeleted = $extraMaterial->delete();
            if ($extraMaterialDeleted) {
                return response()->json('sucesso');
            }

            return response()->json('error');
        } catch (Exception $e) {
            Log::error('Erro ao tentar excluir ExtraMaterial (ProjectsController - deleteExtraMaterial)');
            report($e);
        }
    }
}
