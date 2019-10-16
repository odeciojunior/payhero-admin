<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\DomainRecord;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Exceptions\Services\ServiceException;
use Modules\Projects\Transformers\ProjectsResource;
use Modules\Projects\Transformers\ProjectsSelectResource;

/**
 * Class ProjectService
 * @package Modules\Core\Services
 */
class ProjectService
{
    /**
     * @var Project
     */
    private $projectModel;
    /**
     * @var ShopifyIntegration
     */
    private $shopifyIntegrationModel;
    /**
     * @var CloudFlareService
     */
    private $cloudFlareService;
    /**
     * @var SendgridService
     */
    private $sendgridService;
    /**
     * @var DomainRecord
     */
    private $domainRecordModel;
    /**
     * @var ShopifyService
     */
    private $shopifyService;

    /**
     * @param string|null $urlStore
     * @param string|null $token
     * @return ShopifyService
     */
    private function getShopifyService(string $urlStore = null, string $token = null)
    {
        if (!$this->shopifyService) {
            $this->shopifyService = new ShopifyService($urlStore, $token);
        }

        return $this->shopifyService;
    }

    /**
     * @return Application|mixed
     */
    function getShopifyIntegration()
    {
        if (!$this->shopifyIntegrationModel) {
            $this->shopifyIntegrationModel = app(ShopifyIntegration::class);
        }

        return $this->shopifyIntegrationModel;
    }

    /**
     * @return Application|mixed
     */
    private function getCloudFlareService()
    {
        if (!$this->cloudFlareService) {
            $this->cloudFlareService = app(CloudFlareService::class);
        }

        return $this->cloudFlareService;
    }

    /**
     * @return Application|mixed|SendgridService
     */
    private function getSendgridService()
    {
        if (!$this->sendgridService) {
            $this->sendgridService = app(SendgridService::class);
        }

        return $this->sendgridService;
    }

    /**
     * @return Application|mixed
     */
    private function getDomainRecordModel()
    {
        if (!$this->domainRecordModel) {
            $this->domainRecordModel = app(DomainRecord::class);
        }

        return $this->domainRecordModel;
    }

    /**
     * @return Application|mixed
     */
    function getProjectModel()
    {
        if (!$this->projectModel) {
            $this->projectModel = app(Project::class);
        }

        return $this->projectModel;
    }

    /**
     * @param $projectId
     * @return mixed
     * @throws ServiceException
     */
    public function hasSales($projectId)
    {
        try {

            return $this->getProjectModel()
                        ->has('sales')
                        ->where('id', $projectId)
                        ->count();
        } catch (Exception $e) {
            Log::warning('ProjectService - Erro ao remover projeto');
            report($e);

            throw new ServiceException('ProjectService - hasSales - ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $projectId
     * @return bool
     * @throws ServiceException
     */
    public function delete($projectId)
    {
        try {

            $project = $this->getProjectModel()
                            ->with([
                                       'domains',
                                       'shopifyIntegrations',
                                       'plans',
                                       'plans.productsPlans',
                                       'plans.productsPlans.product',
                                       'pixels',
                                       'discountCoupons',
                                       'shippings',
                                       'usersProjects',
                                   ])
                            ->where('id', $projectId)->first();

            if ($project) {
                //projeto encontrado
                $countSales = $this->hasSales($project->id);

                if ($countSales == 0) {
                    //n tem vendas

                    if (!empty($project->shopify_id)) {

                        if (!empty($project->plans)) {
                            foreach ($project->plans as $plan) {
                                foreach ($plan->productsPlans as $productsPlan) {
                                    if (!empty($productsPlan->product)) {
                                        $productsPlan->product->delete();
                                        $productsPlan->delete();
                                    }
                                }
                            }
                        }
                    } else {
                        foreach ($project->plans as $plan) {
                            foreach ($plan->productsPlans as $productsPlan) {
                                $productsPlan->delete();
                            }
                        }
                    }

                    if (!empty($project->plans) && $project->plans->isNotEmpty()) {
                        foreach ($project->plans as $plan) {
                            $plan->delete();
                        }
                    }

                    if (!empty($project->pixels) && $project->pixels->isNotEmpty()) {
                        foreach ($project->pixels as $pixel) {
                            $pixel->delete();
                        }
                    }

                    if (!empty($project->discountCoupons) && $project->discountCoupons->isNotEmpty()) {
                        foreach ($project->discountCoupons as $discountCoupon) {
                            $discountCoupon->delete();
                        }
                    }

                    if (!empty($project->zenviaSms) && $project->zenviaSms->isNotEmpty()) {
                        foreach ($project->zenviaSms as $zenviaSms) {
                            $zenviaSms->delete();
                        }
                    }

                    if (!empty($project->shippings) && $project->shippings->isNotEmpty()) {
                        foreach ($project->shippings as $shipping) {
                            $shipping->delete();
                        }
                    }

                    if (!empty($project->usersProjects) && $project->usersProjects->isNotEmpty()) {
                        foreach ($project->usersProjects as $usersProject) {
                            $usersProject->delete();
                        }
                    }

                    foreach ($project->domains as $domain) {

                        $this->getCloudFlareService()->deleteZoneById($domain->cloudflare_domain_id);
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
                                    if (!empty($shopifyIntegration->theme_html)) {
                                        $shopify->setTemplateHtml($shopifyIntegration->theme_file, $shopifyIntegration->theme_html);
                                    }
                                    if (!empty($shopifyIntegration->layout_theme_html)) {
                                        $shopify->setTemplateHtml('layout/theme.liquid', $shopifyIntegration->layout_theme_html);
                                    }


                                }
                            } catch (Exception $e) {
                                //remover integração do shopify

//                                $this->getShopifyIntegration()
//                                     ->where('project_id', $project->id)
//                                     ->delete();

//                                $shopifyIntegration->delete();
//
//                                $projectDeleted = $project->delete();

                                //throwl
//                                throw new ServiceException('ProjectService - delete - erro ao mudar template ' . $e->getMessage(), $e->getCode(), $e);
                            }
                        }
                    }
                    //remover integração do shopify
                    $this->getShopifyIntegration()
                         ->where('project_id', $project->id)
                         ->delete();

                    $projectDeleted = $project->delete();
                    if ($projectDeleted) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    //tem venda
                    return false;
                }
            } else {
                //projeto nao encontrado
                return false;
            }
        } catch (Exception $e) {

            throw new ServiceException('ProjectService - Erro ao remover projeto - ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $pagination
     * @return AnonymousResourceCollection
     */
    public function getUserProjects(string $pagination)
    {
        $projectModel     = new Project();
        $userProjectModel = new UserProject();

        $userProjects = $userProjectModel->where('user_id', auth()->user()->id)->pluck('project_id');
        $projects     = $projectModel->whereIn('id', $userProjects)->orderBy('id', 'DESC');
        if ($pagination) {
            return ProjectsSelectResource::collection($projects->get());
        } else {
            return ProjectsResource::collection($projects->paginate(10));
        }
    }
}
