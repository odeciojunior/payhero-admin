<?php

namespace Modules\Projects\Http\Controllers;

use App\Entities\Carrier;
use App\Entities\DomainRecord;
use App\Entities\ExtraMaterial;
use App\Entities\Project;
use App\Entities\UserProject;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\ShopifyService;
use Modules\Projects\Http\Requests\ProjectStoreRequest;
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
     * @var SendgridService
     */
    private $sendgridService;
    /**
     * @var CloudFlareService
     */
    private $cloudFlareService;
    /**
     * @var $domainRecordModel
     */
    private $domainRecordModel;
    /**
     * @var ShopifyService
     */
    private $shopifyService;

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
     * @return \Illuminate\Contracts\Foundation\Application|mixed|ShopifyService
     */
    private function getShopifyService(string $urlStore = null, string $token = null)
    {
        if (!$this->shopifyService) {
            $this->shopifyService = new ShopifyService($urlStore, $token);
        }

        return $this->shopifyService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDomainRecordModel()
    {
        if (!$this->domainRecordModel) {
            $this->domainRecordModel = app(DomainRecord::class);
        }

        return $this->domainRecordModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|SendgridService
     */
    private function getSendgridService()
    {
        if (!$this->sendgridService) {
            $this->sendgridService = app(SendgridService::class);
        }

        return $this->sendgridService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|CloudFlareService
     */
    private function getCloudFlareService()
    {
        if (!$this->cloudFlareService) {
            $this->cloudFlareService = app(CloudFlareService::class);
        }

        return $this->cloudFlareService;
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
    public function store(ProjectStoreRequest $request)
    {
        try {
            $requestValidated = $request->validated();
            if ($requestValidated) {
                $requestValidated['company'] = current(Hashids::decode($requestValidated['company']));

                $project = $this->getProject()->create([
                                                           'name'                       => $requestValidated['name'],
                                                           'description'                => $requestValidated['description'],
                                                           'installments_amount'        => 12,
                                                           'installments_interest_free' => 12,
                                                           'visibility'                 => 'private',
                                                       ]);

                if ($project) {
                    $photo = $request->file('photo-main');
                    if ($photo != null) {
                        try {
                            $img = Image::make($photo->getPathname());
                            $img->crop($requestValidated['photo_w'], $requestValidated['photo_h'], $requestValidated['photo_x1'], $requestValidated['photo_y1']);
                            $img->save($photo->getPathname());

                            $digitalOceanPath = $this->getDigitalOceanFileService()
                                                     ->uploadFile("uploads/user/" . Hashids::encode(auth()->user()->id) . '/public/projects/' . $project->id_code . '/main', $photo);
                            $project->update(['photo' => $digitalOceanPath]);
                        } catch (Exception $e) {
                            Log::warning('Erro ao tentar salvar foto projeto - ProjectsController - store');
                            report($e);
                        }
                    }

                    $userProject = $this->getUserProject()->create([
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
            $user = auth()->user()->load('companies');

            $idProject = current(Hashids::decode($id));
            $project   = $this->getProject()->with([
                                                       'usersProjects' => function($query) use ($user, $idProject) {
                                                           $query->where('user', $user->id)
                                                                 ->where('project', $idProject)->first();
                                                       },
                                                   ])->find($idProject);

            $view = view('projects::edit', [
                'companies' => $user->companies,
                'project'   => $project,
            ]);

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
                            $this->getDigitalOceanFileService()->deleteFile($project->photo);
                            $img = Image::make($projectPhoto->getPathname());
                            $img->crop($requestValidated['photo_w'], $requestValidated['photo_h'], $requestValidated['photo_x1'], $requestValidated['photo_y1']);
                            $img->resize(300, 300);
                            $img->save($projectPhoto->getPathname());

                            $digitalOceanPath = $this->getDigitalOceanFileService()
                                                     ->uploadFile('uploads/user/' . auth()->user()->id_code . '/public/projects/' . $project->id_code . '/main', $projectPhoto);
                            $project->update([
                                                 'photo' => $digitalOceanPath,
                                             ]);
                        }

                        $projectLogo = $request->file('logo');
                        if ($projectLogo != null) {

                            $this->getDigitalOceanFileService()->deleteFile($project->logo);
                            $img = Image::make($projectLogo->getPathname());

                            $img->resize(null, 300, function($constraint) {
                                $constraint->aspectRatio();
                            });

                            $img->save($projectLogo->getPathname());

                            $digitalOceanPathLogo = $this->getDigitalOceanFileService()
                                                         ->uploadFile('uploads/user/' . auth()->user()->id_code . '/public/projects/' . $project->id_code . '/logo', $projectLogo);

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

                    $requestValidated['company'] = current(Hashids::decode($requestValidated['company']));
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

            $project = $this->getProject()
                            ->with(['domains', 'shopifyIntegrations', 'plans', 'pixels', 'discountCoupons', 'zenviaSms', 'shippings'])
                            ->where('id', $idProject)->first();

            $deletedDependecis = $this->deleteDependences($project);

            try {

                foreach ($project->domains as $domain) {

                    if ($this->getCloudFlareService()->deleteZone($domain->name)) {
                        //zona deletada
                        $this->getSendgridService()->deleteLinkBrand($domain->name);
                        $this->getSendgridService()->deleteZone($domain->name);

                        $recordsDeleted = $this->getDomainRecordModel()->where('domain_id', $domain->id)->delete();
                        $domainDeleted  = $domain->delete();

                        if (!empty($project->shopify_id)) {
                            //se for shopify, voltar as integraçoes ao html padrao
                            try {

                                foreach ($project->shopifyIntegrations as $shopifyIntegration) {
                                    $shopify = $this->getShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                                    $shopify->setThemeByRole('main');
                                    $shopify->setTemplateHtml($shopifyIntegration->theme_file, $shopifyIntegration->theme_html);
                                    $shopify->setTemplateHtml('layout/theme.liquid', $shopifyIntegration->layout_theme_html);
                                    $shopifyIntegration->delete();
                                }
                            } catch (\Exception $e) {
                                //throwl

                            }
                        }
                    } else {
                        //erro ao deletar zona
                        //log error?
                    }
                }

                /*
                if ($project->photo != null) {
                    $this->getDigitalOceanFileService()->deleteFile($project->photo);
                }

                if ($project->logo != null) {
                    $this->getDigitalOceanFileService()->deleteFile($project->logo);
                }
                */
            } catch (Exception $e) {
                Log::warning('ProjectController - destroy - Erro ao deletar foto e logo do project');
                report($e);
            }

            if ($deletedDependecis) {
                $projectDeleted = $project->delete();
                if ($projectDeleted) {
                    return response()->json('success', 200);
                }
            }

            return response()->json('error', 422);
        } catch (Exception $e) {
            Log::warning('ProjectController - delete - Erro ao deletar project');
            report($e);
        }
    }

    public function deleteDependences(Project $project)
    {

        if (isset($project->plans)) {
            foreach ($project->plans as $plan) {
                $plan->delete();
            }
        }

        if (isset($project->pixels)) {
            foreach ($project->pixels as $pixel) {
                $pixel->delete();
            }
        }

        if (isset($project->discountCoupons)) {
            foreach ($project->discountCoupons as $discountCoupon) {
                $discountCoupon->delete();
            }
        }

        if (isset($project->zenviaSms)) {
            foreach ($project->zenviaSms as $zenviaSms) {
                $zenviaSms->delete();
            }
        }

        if (isset($project->shippings)) {
            foreach ($project->shippings as $shipping) {
                $shipping->delete();
            }
        }

        return true;
    }
}
