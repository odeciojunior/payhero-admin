<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\Domain;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\SendgridService;
use Modules\Core\Events\DomainApprovedEvent;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Entities\ShopifyIntegration;

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
     * @return ShopifyIntegration|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getShopifyIntegrationModel()
    {
        if (!$this->shopifyIntegrationModel) {
            $this->shopifyIntegrationModel = app(ShopifyIntegration::class);
        }

        return $this->shopifyIntegrationModel;
    }

    /**
     * @return Domain|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDomainModel()
    {
        if (!$this->domainModel) {
            $this->domainModel = app(Domain::class);
        }

        return $this->domainModel;
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
     * @param null $domainId
     * @param bool $reCheck
     * @return bool
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function verifyPendingDomains($domainId = null, $reCheck = false)
    {
        try {
            //verifica todos os dominios pendentes
            $domains = $this->getDomainModel()
                            ->with([
                                       'project',
                                       'project.shopifyIntegrations',
                                       'project.users',
                                   ]);
            if (!$reCheck) {
                $domains->where('status', '!=', $this->getDomainModel()->present()->getStatus('approved'));
            }

            if (!empty($domainId)) {
                $domains->where('id', $domainId);
            }
            $domains = $domains->get();

            foreach ($domains as $domain) {

                if ($this->getCloudFlareService()
                         ->checkHtmlMetadata('https://checkout.' . $domain->name, 'checkout-cloudfox', '1')) {

                    $responseValidateDomain = null;
                    $responseValidateLink   = null;

                    $linkBrandResponse = $this->getSendgridService()->getLinkBrand($domain->name);
                    $sendgridResponse  = $this->getSendgridService()->getZone($domain->name);

                    if (!empty($linkBrandResponse) && !empty($sendgridResponse)) {
                        $responseValidateDomain = $this->getSendgridService()->validateDomain($sendgridResponse->id);
                        $responseValidateLink   = $this->getSendgridService()
                                                       ->validateBrandLink($linkBrandResponse->id);
                    }

                    if ($responseValidateDomain && $responseValidateLink) {
                        $domain->update([
                                            'status' => $this->getDomainModel()->present()->getStatus('approved'),
                                        ]);
                        Log::warning('domains update command aqui 1');
                    }

                    if (!empty($domain->project->shopify_id)) {
                        Log::warning('domains update command if ...');

                        //dominio shopify, fazer as alteracoes nos templates
                        foreach ($domain->project->shopifyIntegrations as $shopifyIntegration) {

                            try {
                                Log::warning('domains update command foreach ...');

                                $shopify = $this->getShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                                $shopify->setThemeByRole('main');

                                $htmlCart = $shopify->getTemplateHtml($shopify::templateKeyName);
                                if ($htmlCart) {
                                    //template normal

                                    if ($shopify->checkCartTemplate($htmlCart)) {
                                        $domain->update([
                                                            'status' => $this->getDomainModel()->present()
                                                                             ->getStatus('approved'),
                                                        ]);

                                        return true;
                                    } else {

                                        //template normal
                                        Log::warning('domains update command tema normal ...');

                                        $shopifyIntegration->update([
                                                                        'theme_type' => $this->getShopifyIntegrationModel()
                                                                                             ->present()
                                                                                             ->getThemeType('basic_theme'),
                                                                        'theme_name' => $shopify->getThemeName(),
                                                                        'theme_file' => 'sections/cart-template.liquid',
                                                                        'theme_html' => $htmlCart,
                                                                    ]);

                                        $shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart, $domain->name);
                                    }
                                } else {
                                    //template ajax
                                    Log::warning('domains update command tema ajax ...');

                                    $htmlCart = $shopify->getTemplateHtml($shopify::templateAjaxKeyName);

                                    $shopifyIntegration->update([
                                                                    'theme_type' => $this->getShopifyIntegrationModel()->present()
                                                                                         ->getThemeType('ajax_theme'),
                                                                    'theme_name' => $shopify->getThemeName(),
                                                                    'theme_file' => 'snippets/ajax-cart-template.liquid',
                                                                    'theme_html' => $htmlCart,
                                                                ]);

                                    $shopify->updateTemplateHtml('snippets/ajax-cart-template.liquid', $htmlCart, $domain->name, true);
                                }

                                //inserir o javascript para o trackeamento (src, utm)
                                $htmlBody = $shopify->getTemplateHtml('layout/theme.liquid');
                                if ($htmlBody) {
                                    //template do layout
                                    $shopifyIntegration->update([
                                                                    'layout_theme_html' => $htmlBody,
                                                                ]);

                                    $shopify->insertUtmTracking('layout/theme.liquid', $htmlBody);
                                }
                            } catch (\Exception $e) {
                                Log::warning('Erro ao processar o html na integracao do shopify');
                                report($e);
                                //throwl

                            }
                        }
                    }

                    //integracao no shopify funcionou? aprova o dominio
                    $domain->update([
                                        'status' => $this->getDomainModel()->present()->getStatus('approved'),
                                    ]);

                    event(new DomainApprovedEvent($domain, $domain->project, $domain->project->users));

                    Log::warning('domains update command final');
                } else {
                    $domain->update([
                                        'status' => $this->getDomainModel()->present()->getStatus('pending'),
                                    ]);
                    Log::warning('domains update command final else');

                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            $domain->update([
                                'status' => $this->getDomainModel()->present()->getStatus('pending'),
                            ]);

            Log::warning('DomainService - Erro ao verificar dominios pendentes');
            report($e);

            return false;
        }
    }
}
