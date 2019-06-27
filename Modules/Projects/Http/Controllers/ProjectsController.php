<?php

namespace Modules\Projects\Http\Controllers;

use App\Entities\Carrier;
use App\Entities\ExtraMaterial;
use App\Entities\Project;
use App\Entities\UserProject;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Projects\Http\Requests\ProjectUpdateRequest;
use Vinkla\Hashids\Facades\Hashids;

class ProjectsController extends Controller
{
    /**
     * @var Project
     */
    private $projectModel;
    /**
     * @var UserProject
     */
    private $userProjectModel;
    /**
     * @var Carrier
     */
    private $carrierModel;
    /**
     * @var ExtraMaterial
     */
    private $extraMaterialsModel;
    /**
     * @var DigitalOceanFileService
     */
    private $digitalOceanFileService;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    function getProject()
    {
        if (!$this->projectModel) {
            $this->projectModel = app(Project::class);
        }

        return $this->projectModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getUserProject()
    {
        if (!$this->userProjectModel) {
            $this->userProjectModel = app(UserProject::class);
        }

        return $this->userProjectModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getCarrier()
    {
        if (!$this->carrierModel) {
            $this->carrierModel = app(Carrier::class);
        }

        return $this->carrierModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
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

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $projects = $this->getProject()->whereHas('usersProjects', function($query) {
                $query->where('user', auth()->user()->id);
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
                    Log::warning('ProjectController - store - Erro ao enviar foto do project');
                    report($e);
                }
            }

            return redirect()->route('projects.index');
        } catch (Exception $e) {
            Log::error('Erro ao tentar salvar projeto (ProjectsController - store)' . $e->getFile());
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

                $user      = auth()->user()->load('companies');
                $companies = $user->companies;

                $project = $this->getProject()->where('id', $idProject)->first();

                if ($project) {

                    return view('projects::project', ['project' => $project, 'companies' => $companies]);
                }

                return redirect()->route('projects.index');
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar detalhes do projeto (ProjectsController - show)');
            report($e);
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
            $user      = auth()->user()->load('companies');
            $idProject = current(Hashids::decode($id));
            $project   = $this->getProject()->with([
                                                       'usersProjects' => function($query) use ($user, $idProject) {
                                                           $query->where('user', $user->id)
                                                                 ->where('project', $idProject)->first();
                                                       },
                                                   ])->where('id', $idProject)->first();

            $view = view('projects::edit', compact([
                                                       'companies' => $user->companies,
                                                       'project'   => $project,
                                                   ]));

            return response()->json($view->render());
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

            $requestValidated = $request->validated();
            if ($requestValidated) {
                $project = $this->getProject()->where('id', Hashids::decode($id))->first();

                $requestValidated['company'] = current(Hashids::decode($requestValidated['company']));
                if (!$requestValidated['shipment']) {
                    $requestValidated['carrier']              = null;
                    $requestValidated['shipment_responsible'] = null;
                }

                if ($requestValidated['installments_amount'] < $requestValidated['installments_interest_free']) {
                    $requestValidated['installments_interest_free'] = $requestValidated['installments_amount'];
                }
                $requestValidated['cookie_duration'] = 60;
                $projectUpdate                       = $project->update($requestValidated);
                if ($projectUpdate) {
                    try {
                        $projectPhoto = $request->file('photo');
                        if ($projectPhoto != null) {
                            $this->getDigitalOceanFileService()->deleteFile($project->photo);

                            $img = Image::make($projectPhoto->getPathname());
                            $img->crop($requestValidated['photo_w'], $requestValidated['photo_h'], $requestValidated['photo_x1'], $requestValidated['photo_y1']);
                            $img->resize(200, 200);
                            $img->save($projectPhoto->getPathname());

                            $digitalOceanPath = $this->getDigitalOceanFileService()
                                                     ->uploadFile('uploads/user/' . auth()->user()->id_code . '/public/projects' . $project->id_code . '/main', $projectPhoto);
                            $project->update([
                                                 'photo' => $digitalOceanPath,
                                             ]);
                        }

                        $projectLogo = $request->file('logo');
                        if ($projectLogo != null) {
                            $this->getDigitalOceanFileService()->deleteFile($project->logo);
                            $img = Image::make($projectLogo->getPathname());
                            $img->crop($requestValidated['logo_w'], $requestValidated['logo_h'], $requestValidated['logo_x1'], $requestValidated['logo_y1']);
                            $img->resize(200, 200);
                            $img->save($projectLogo->getPathname());

                            $digitalOceanPathLogo = $this->getDigitalOceanFileService()
                                                         ->uploadFile('uploads/user/' . auth()->user()->id_code . '/public/projects' . $project->id_code . '/logo', $projectLogo);

                            $project->update([
                                                 'logo' => $digitalOceanPathLogo,
                                             ]);
                        }
                    } catch (Exception $e) {
                        Log::warning('ProjectController - update - Erro ao enviar foto');
                        report($e);
                    }

                    $userProject = $this->getUserProject()->where([
                                                                      ['user', auth()->user()->id],
                                                                      ['project', $project->id],
                                                                  ])->first();

                    if ($userProject->company != $requestValidated['company']) {
                        $userProject->update(['company' => $requestValidated['company']]);
                    }

                    return response()->json('success', 200);
                }
            }

            return response()->json('error', 422);
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
            $idProject = current(Hashids::decode($id));

            $project = $this->getProject()->where('id', $idProject)->first();
            try {

                if ($project->photo != null) {
                    $this->getDigitalOceanFileService()->deleteFile($project->photo);
                }

                if ($project->logo != null) {
                    $this->getDigitalOceanFileService()->deleteFile($project->logo);
                }
            } catch (Exception $e) {
                Log::warning('ProjectController - destroy - Erro ao deletar foto e logo do project');
                report($e);
            }
            $projectDeleted = $project->delete();
            if ($projectDeleted) {
                return response()->json('success', 200);
            }

            return response()->json('error', 422);
        } catch (Exception $e) {
            Log::warning('ProjectController - delete - Erro ao deletar project');
            report($e);
        }
    }
    /*public function getDadosProject($id)
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
    }*/
}
