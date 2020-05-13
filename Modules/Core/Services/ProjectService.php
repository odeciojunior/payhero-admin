<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\DomainRecord;
use Modules\Core\Entities\ProjectUpsellConfig;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Exceptions\Services\ServiceException;
use Modules\Projects\Transformers\ProjectsResource;
use Modules\Projects\Transformers\ProjectsSelectResource;
use DB;

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
                                       'notifications',
                                       'affiliateRequests',
                                       'affiliates',
                                       'affiliates.affiliateLinks',
                                       'upsellConfig',
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
                $shopifyIntegration = $this->getShopifyIntegration()
                                           ->where('project_id', $project->id)->first();

                if (!empty($shopifyIntegration)) {
                    $shopifyIntegration->delete();
                }

                $products = Product::where('project_id', $project->id)->get();

                foreach ($products as $product) {
                    $product->update([
                                         'shopify_variant_id' => '',
                                         'shopify_id'         => '',
                                     ]);
                }

                $plans = Plan::where('project_id', $project->id)->get();

                foreach ($plans as $plan) {
                    $plan->update([
                                      'shopify_variant_id' => '',
                                      'shopify_id'         => '',
                                  ]);
                }

                if (!empty($project->notifications) && $project->notifications->isNotEmpty()) {
                    foreach ($project->notifications as $notification) {
                        $notification->delete();
                    }
                }

                if (!empty($project->affiliateRequests) && $project->affiliateRequests->isNotEmpty()) {
                    foreach ($project->affiliateRequests as $affiliateRequests) {
                        $affiliateRequests->delete();
                    }
                }

                if (!empty($project->affiliates) && $project->affiliates->isNotEmpty()) {
                    foreach ($project->affiliates as $affiliate) {
                        if (!empty($affiliate->affiliateLinks) && $affiliate->affiliateLinks->isNotEmpty()) {
                            foreach ($affiliate->affiliateLinks as $affiliateLink) {
                                $affiliateLink->delete();
                            }
                        }
                        $affiliate->delete();
                    }
                }

                if (!empty($project->upsellConfig)) {
                    $upsellConfig = $project->upsellConfig;
                    $upsellConfig->delete();
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
     * @param array $status
     * @param bool $affiliate
     * @return AnonymousResourceCollection
     */
    public function getUserProjects(string $pagination, array $status, $affiliate = false)
    {
        $userId = auth()->user()->account_owner_id;

        if ($affiliate) {
            $projects = Project::leftJoin('users_projects', function($join) use($userId) {
                                    $join->on('projects.id', '=', 'users_projects.project_id')
                                        ->where('users_projects.user_id', $userId)
                                        ->whereNull('users_projects.deleted_at');
                                })
                                ->leftJoin('affiliates', function($join) use($userId) {
                                    $join->on('projects.id', '=', 'affiliates.project_id')
                                        ->where('affiliates.user_id', $userId)
                                        ->whereNull('affiliates.deleted_at');
                                })
                               ->select('projects.*', 'affiliates.created_at as affiliate_created_at', 'affiliates.percentage as affiliate_percentage',
                                    'affiliates.status_enum as affiliate_status',
                                    DB::raw('CASE WHEN affiliates.id IS NOT NULL THEN affiliates.id ELSE 0 END AS affiliate_id'),
                                    DB::raw('CASE WHEN affiliates.order_priority IS NOT NULL THEN affiliates.order_priority ELSE users_projects.order_priority END AS order_p'))
                               ->whereIn('projects.status', $status)
                               ->where('users_projects.user_id', $userId)
                               ->orWhere('affiliates.user_id', $userId)
                               ->orderBy('projects.status')
                               ->orderBy('order_p')
                               ->orderBy('projects.id', 'DESC');

        } else {
            $projects = Project::leftJoin('users_projects','projects.id', '=', 'users_projects.project_id')
                               ->select('projects.*', 'users_projects.order_priority as order_p')
                               ->whereIn('projects.status', $status)
                               ->where('users_projects.user_id', $userId)
                               ->whereNull('users_projects.deleted_at')
                               ->orderBy('projects.status')
                               ->orderBy('order_p')
                               ->orderBy('projects.id', 'DESC');
        }

        if ($pagination) {
            return ProjectsSelectResource::collection($projects->get());
        } else {
            return ProjectsResource::collection($projects->paginate(10));
        }
    }

    public function createUpsellConfig($projectId)
    {
        try {
            $projectUpsellConfigModel = new ProjectUpsellConfig();

            $projectUpsellConfigModel->create([
                                                  'project_id'     => $projectId,
                                                  'header'         => '(Mensagem do cabeçalho) Ex: Você não pode perder essa promoção...',
                                                  'title'          => '(Título principal) Ex: Ganhe 30% de desconto...',
                                                  'description'    => '(Descrição) Ex: Como você comprou esse produto, nós achamos que você poderia se interessar por...',
                                                  'countdown_time' => null,
                                                  'countdown_flag' => 0,
                                              ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
