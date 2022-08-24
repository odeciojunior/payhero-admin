<?php

namespace Modules\Core\Services;

use DB;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\DomainRecord;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ProjectUpsellConfig;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\WooCommerceService;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Sale;
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
     * @var WooCommerceService
     */
    private $wooCommerceService;
    /**
     * @var WooCommerceIntegration
     */
    private $wooCommerceIntegrationModel;

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
     * @param string|null $urlStore
     * @param string|null $tokenUser
     * @param string|null $tokenPass
     * @return WooCommerceService
     */
    private function getWooCommerceService(string $urlStore = null, string $tokenUser = null, string $tokenPass = null)
    {
        if (!$this->wooCommerceService) {
            $this->wooCommerceService = new WooCommerceService($urlStore, $tokenUser, $tokenPass);
        }

        return $this->wooCommerceService;
    }

    /**
     * @return Application|mixed
     */
    function getWooCommerceIntegration()
    {
        if (!$this->wooCommerceIntegrationModel) {
            $this->wooCommerceIntegrationModel = app(WooCommerceIntegration::class);
        }

        return $this->wooCommerceIntegrationModel;
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
                ->has("sales")
                ->where("id", $projectId)
                ->count();
        } catch (Exception $e) {
            Log::warning("ProjectService - Erro ao remover projeto");
            report($e);

            throw new ServiceException("ProjectService - hasSales - " . $e->getMessage(), $e->getCode(), $e);
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
                    "domains",
                    "shopifyIntegrations",
                    "plans",
                    "plans.productsPlans",
                    "plans.productsPlans.product",
                    "pixels",
                    "discountCoupons",
                    "shippings",
                    "usersProjects",
                    "notifications",
                    "affiliateRequests",
                    "affiliates",
                    "affiliates.affiliateLinks",
                    "upsellConfig",
                    "checkoutConfig",
                ])
                ->where("id", $projectId)
                ->first();

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
                    try {
                        $domainService = new DomainService();
                        $deleteDomain = $domainService->deleteDomain($domain);

                        if (!$deleteDomain["success"]) {
                            report(
                                ["Erro ao excluir dominio do projetoId: " . $project->id] . ", domain: " . $domain->name
                            );
                        }
                    } catch (Exception $e) {
                        report(["Erro ao excluir dominio do projetoId: " . $project->id, $e]);
                    }
                }

                //remover integração do woocommerce
                $wooCommerceIntegration = $this->getWooCommerceIntegration()
                    ->where("project_id", $project->id)
                    ->first();

                if (!empty($wooCommerceIntegration)) {
                    $wooCommerceIntegration->delete();
                    $wooCommerceService = $this->getWooCommerceService(
                        $wooCommerceIntegration->url_store,
                        $wooCommerceIntegration->token_user,
                        $wooCommerceIntegration->token_pass
                    );

                    $wooCommerceService->deleteHooks($project->id);
                }
                //end woo

                //remover integração do shopify
                $shopifyIntegration = $this->getShopifyIntegration()
                    ->where("project_id", $project->id)
                    ->first();

                if (!empty($shopifyIntegration)) {
                    $shopifyIntegration->delete();
                }

                $products = Product::where("project_id", $project->id)->get();

                foreach ($products as $product) {
                    $product->update([
                        "shopify_variant_id" => "",
                        "shopify_id" => "",
                    ]);
                }

                $plans = Plan::where("project_id", $project->id)->get();

                foreach ($plans as $plan) {
                    $plan->update([
                        "shopify_variant_id" => "",
                        "shopify_id" => "",
                    ]);
                }
                //end shopify

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
                    "name" => $project->name . " (Excluído)",
                    "status" => (new Project())->present()->getStatus("disabled"),
                ]);

                if ($projectUpdated) {
                    return true;
                } else {
                    report("Erro ao atualizar nome e status do projeto: id-> (" . $project->id . ")");

                    return false;
                }
            } else {
                //projeto nao encontrado
                return false;
            }
        } catch (Exception $e) {
            throw new ServiceException(
                "ProjectService - Erro ao remover projeto - " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param string $pagination
     * @param array $status
     * @param bool $affiliate
     * @return AnonymousResourceCollection
     */
    public function getUserProjects(string $pagination, array $status, $affiliate = false, $companyId = '')
    {
        $userId = auth()->user()->getAccountOwnerId();

        if ($affiliate) {
            $projects = Project::leftJoin(
                'users_projects',
                function ($join) use ($userId, $companyId) {
                    if(!empty($companyId)){
                        $join->on('projects.id', '=', 'users_projects.project_id')
                            ->where('users_projects.company_id', $companyId)
                            ->where('users_projects.user_id', $userId)
                            ->whereNull('users_projects.deleted_at');
                    }
                    else{
                        $join->on('projects.id', '=', 'users_projects.project_id')
                            ->where('users_projects.user_id', $userId)
                            ->whereNull('users_projects.deleted_at');
                    }
                }
            )
                ->leftJoin(
                    'affiliates',
                    function ($join) use ($userId, $companyId) {
                        $join->on('projects.id', '=', 'affiliates.project_id')
                            ->where('affiliates.company_id', $companyId)
                            ->where('affiliates.user_id', $userId)
                            ->whereNull('affiliates.deleted_at');
                    }
                )
                ->select(
                    "projects.*",
                    "affiliates.created_at as affiliate_created_at",
                    "affiliates.percentage as affiliate_percentage",
                    "affiliates.status_enum as affiliate_status",
                    DB::raw("CASE WHEN affiliates.id IS NOT NULL THEN affiliates.id ELSE 0 END AS affiliate_id"),
                    DB::raw(
                        "CASE WHEN affiliates.order_priority IS NOT NULL THEN affiliates.order_priority ELSE users_projects.order_priority END AS order_p"
                    )
                )
                ->whereIn('projects.status', $status)
                ->where('users_projects.user_id', $userId)
                ->orWhere('affiliates.user_id', $userId);
        } else {
            $projects = Project::leftJoin('users_projects',
                function ($join) use ($userId, $companyId) {
                    if(!empty($companyId)){
                        $join->on('projects.id', '=', 'users_projects.project_id')
                            ->where('users_projects.company_id', $companyId)
                            ->where('users_projects.user_id', $userId)
                            ->whereNull('users_projects.deleted_at');
                    }
                    else{
                        $join->on('projects.id', '=', 'users_projects.project_id')
                            ->where('users_projects.user_id', $userId)
                            ->whereNull('users_projects.deleted_at');
                    }
                }
            )
                ->select('projects.*', 'users_projects.order_priority as order_p')
                ->whereIn('projects.status', $status)
                ->where('users_projects.user_id', $userId)
                ->whereNull('users_projects.deleted_at');
        }

        $projects = $projects->orderBy('projects.status')
            ->orderBy('order_p')
            ->orderBy('projects.id', 'DESC');

        if ($pagination) {
            $projects = $projects->get();
            if(count($projects) == 0) {
                $apiSale = Sale::where('owner_id', $userId)->exists();
                if(!empty($apiSale)) {
                    return response()->json(['data' => 'api sales']);
                }
            }
            return ProjectsSelectResource::collection($projects);
        } else {
            $projects = $projects->with('domains')->get();
            return ProjectsResource::collection($projects);
        }
    }

    public function createUpsellConfig($projectId)
    {
        try {
            $projectUpsellConfigModel = new ProjectUpsellConfig();

            $projectUpsellConfigModel->create([
                "project_id" => $projectId,
                "header" => "(Mensagem do cabeçalho) Ex: Você não pode perder essa promoção...",
                "title" => "(Título principal) Ex: Ganhe 30% de desconto...",
                "description" =>
                    "(Descrição) Ex: Como você comprou esse produto, nós achamos que você poderia se interessar por...",
                "countdown_time" => null,
                "countdown_flag" => 0,
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
