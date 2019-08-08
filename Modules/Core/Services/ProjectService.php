<?php

namespace Modules\Core\Services;

use App\Entities\DomainRecord;
use App\Entities\Project;
use App\Entities\ShopifyIntegration;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Core\Exceptions\Services\ServiceException;

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
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    function getShopifyIntegration()
    {
        if (!$this->shopifyIntegrationModel) {
            $this->shopifyIntegrationModel = app(ShopifyIntegration::class);
        }

        return $this->shopifyIntegrationModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getCloudFlareService()
    {
        if (!$this->cloudFlareService) {
            $this->cloudFlareService = app(CloudFlareService::class);
        }

        return $this->cloudFlareService;
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
     * @return \Illuminate\Contracts\Foundation\Application|mixed
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
                                       'plans.productsPlans.getProduct',
                                       'pixels',
                                       'discountCoupons',
                                       'zenviaSms',
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
                                    if (!empty($productsPlan->getProduct)) {
                                        $productsPlan->getProduct->delete();
                                        $productsPlan->delete();
                                    }
                                }
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

                        $this->getCloudFlareService()->deleteZone($domain->name);
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

                                throw new ServiceException('ProjectService - delete - erro ao mudar template ' . $e->getMessage(), $e->getCode(), $e);
                            }
                        }
                    }
                    //remover integração do shopify
                    $this->getShopifyIntegration()
                         ->where('project', $project->id)
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
}
