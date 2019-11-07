<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Product;
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
            $projectModel = new Project();

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

                if (!empty($project->shippings) && $project->shippings->isNotEmpty()) {
                    foreach ($project->shippings as $shipping) {
                        $shipping->delete();
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
                            Log::warning('Erro ao excluir dominio projeto ' . $project->id);
                        }
                    }
                }
                //remover integração do shopify
                $this->getShopifyIntegration()
                     ->where('project_id', $project->id)
                     ->delete();

                $products = Product::where('project_id', $project->id)->get();

                foreach ($products as $product) {
                    $product->update([
                                         'shopify_variant_id' => '',
                                         'shopify_id'         => '',

                                     ]);
                }

                $projectUpdated = $project->update([
                                                       'name'   => $project->name . ' (Excluído)',
                                                       'status' => $projectModel->present()->getStatus('disabled'),
                                                   ]);

                if ($projectUpdated) {
                    return true;
                } else {
                    Log::warning('Erro ao atualizar nome e status do projeto: id-> (' . $project->id . ')');

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
     * @param string|null $status
     * @return AnonymousResourceCollection
     */
    public function getUserProjects(string $pagination, array $status)
    {
        $projectModel     = new Project();
        $userProjectModel = new UserProject();

        $userProjects = $userProjectModel->where('user_id', auth()->user()->account_owner)->pluck('project_id');
        $projects     = $this->getProjectModel()->whereIn('status', $status)->whereIn('id', $userProjects)
                             ->orderBy('id', 'DESC');
        if ($pagination) {
            return ProjectsSelectResource::collection($projects->get());
        } else {
            return ProjectsResource::collection($projects->paginate(10));
        }
    }
}
