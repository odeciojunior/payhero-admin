<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\DomainRecord;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\User;
use Modules\Core\Events\DomainApprovedEvent;

class DomainService
{
    /**
     * @var Domain
     */
    private $domainModel;
    /**
     * @var CloudFlareService
     */
    private $cloudFlareService;
    /**
     * @var SendgridService
     */
    private $sendgridService;
    /**
     * @var ShopifyIntegration
     */
    private $shopifyIntegrationModel;
    /**
     * @var ShopifyService
     */
    private $shopifyService;
    /**
     * @var Company
     */
    private $companyModel;
    /**
     * @var User
     */
    private $userModel;

    private function getShopifyService(string $urlStore = null, string $token = null)
    {
        if (!$this->shopifyService) {
            $this->shopifyService = new ShopifyService($urlStore, $token);
        }

        return $this->shopifyService;
    }

    private function getShopifyIntegrationModel()
    {
        if (!$this->shopifyIntegrationModel) {
            $this->shopifyIntegrationModel = app(ShopifyIntegration::class);
        }

        return $this->shopifyIntegrationModel;
    }

    private function getDomainModel()
    {
        if (!$this->domainModel) {
            $this->domainModel = app(Domain::class);
        }

        return $this->domainModel;
    }

    private function getCompanyModel()
    {
        if (!$this->companyModel) {
            $this->companyModel = app(Company::class);
        }

        return $this->companyModel;
    }

    private function getUserModel()
    {
        if (!$this->userModel) {
            $this->userModel = app(User::class);
        }

        return $this->userModel;
    }

    private function getCloudFlareService()
    {
        if (!$this->cloudFlareService) {
            $this->cloudFlareService = app(CloudFlareService::class);
        }

        return $this->cloudFlareService;
    }

    private function getSendgridService()
    {
        if (!$this->sendgridService) {
            $this->sendgridService = app(SendgridService::class);
        }

        return $this->sendgridService;
    }

    public function verifyPendingDomains($domainId = null, $reCheck = false)
    {
        try {
            //verifica todos os dominios pendentes registrados na última semana
            $domains = $this->getDomainModel()->with(["project", "project.shopifyIntegrations", "project.users"]);
            if (!$reCheck) {
                $domains->where(
                    "status",
                    "!=",
                    $this->getDomainModel()
                        ->present()
                        ->getStatus("approved")
                );
            }

            if (!empty($domainId)) {
                $domains->where("id", $domainId);
            }
            $domains->where(
                "created_at",
                ">",
                Carbon::today()
                    ->subWeek()
                    ->addDay()
            );
            $domains = $domains->get();

            $userService = new UserService();
            $companyService = new CompanyService();

            foreach ($domains as $domain) {
                if (!$userService->isDocumentValidated($domain->project->users->first()->id)) {
                    continue;
                }

                if (!$companyService->isDocumentValidated($domain->project->usersProjects->first()->company->id)) {
                    continue;
                }

                if (
                    $this->getCloudFlareService()->checkHtmlMetadata(
                        "https://checkout." . $domain->name,
                        "checkout-nexuspay",
                        "1"
                    )
                ) {
                    $responseValidateDomain = null;
                    $responseValidateLink = null;

                    $linkBrandResponse = $this->getSendgridService()->getLinkBrand($domain->name);
                    $sendgridResponse = $this->getSendgridService()->getZone($domain->name);

                    if (!empty($linkBrandResponse) && !empty($sendgridResponse)) {
                        $responseValidateDomain = $this->getSendgridService()->validateDomain($sendgridResponse->id);
                        $responseValidateLink = $this->getSendgridService()->validateBrandLink($linkBrandResponse->id);
                    }

                    if ($responseValidateDomain && $responseValidateLink) {
                        $domain->update([
                            "status" => $this->getDomainModel()
                                ->present()
                                ->getStatus("approved"),
                        ]);
                        TaskService::setCompletedTask(
                            $domain->project->users->first(),
                            Task::find(Task::TASK_DOMAIN_APPROVED)
                        );
                    }

                    if (!empty($domain->project->shopify_id)) {
                        //dominio shopify, fazer as alteracoes nos templates
                        foreach ($domain->project->shopifyIntegrations as $shopifyIntegration) {
                            try {
                                $shopify = $this->getShopifyService(
                                    $shopifyIntegration->url_store,
                                    $shopifyIntegration->token
                                );
                                $basicTheme = $shopifyIntegration->present()->getThemeType("basic_theme");

                                $shopify->templateService->makeTemplateIntegration(
                                    $shopifyIntegration,
                                    $domain,
                                    $basicTheme
                                );
                            } catch (\Exception $e) {
                                report($e);
                            }
                        }
                    }

                    $domain->update([
                        "status" => $this->getDomainModel()
                            ->present()
                            ->getStatus("approved"),
                    ]);

                    event(new DomainApprovedEvent($domain, $domain->project, $domain->project->users));
                    TaskService::setCompletedTask(
                        $domain->project->users->first(),
                        Task::find(Task::TASK_DOMAIN_APPROVED)
                    );
                } else {
                    $domain->update([
                        "status" => $this->getDomainModel()
                            ->present()
                            ->getStatus("pending"),
                    ]);

                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            $domain->update([
                "status" => $this->getDomainModel()
                    ->present()
                    ->getStatus("pending"),
            ]);

            return false;
        }
    }

    public function deleteDomain(Domain $domain)
    {
        try {
            $domain->load("domainsRecords", "project", "project.shopifyIntegrations");

            if (empty($domain->project) && !Gate::allows("edit", [$domain->project])) {
                return [
                    "message" => "Não foi possível deletar o domínio!",
                    "success" => false,
                ];
            }

            if (empty($domain->cloudflare_domain_id)) {
                DomainRecord::where("domain_id", $domain->id)->delete();
                $domain->delete();

                return [
                    "message" => "Dominio removido com sucesso!",
                    "success" => true,
                ];
            }

            $cloudflareService = new CloudFlareService();

            if ($cloudflareService->removeDomain($domain) || empty($cloudflareService->getZones($domain->name))) {
                DomainRecord::where("domain_id", $domain->id)->delete();
                $domain->delete();

                if (!empty($domain->project->shopify_id)) {
                    //se for shopify, voltar as integraçoes ao html padrao
                    try {
                        foreach ($domain->project->shopifyIntegrations as $shopifyIntegration) {
                            $shopify = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                            $shopify->templateService->removeIntegrationInAllThemes();
                        }
                    } catch (Exception $e) {
                        return [
                            "message" => "Domínio removido com sucesso",
                            "success" => true,
                        ];
                    }
                }

                return [
                    "message" => "Domínio removido com sucesso",
                    "success" => true,
                ];
            } else {
                //erro ao deletar zona
                return [
                    "message" => "Não foi possível deletar o domínio!",
                    "success" => false,
                ];
            }
        } catch (Exception $e) {
            $message = CloudflareErrorsService::formatErrorException($e);

            return [
                "message" => $message,
                "success" => false,
            ];
        }
    }
}
