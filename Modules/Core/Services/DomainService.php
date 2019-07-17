<?php

namespace Modules\Core\Services;

use App\Entities\Domain;
use App\Entities\ShopifyIntegration;
use Exception;
use Illuminate\Support\Facades\Log;

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
     * @return bool
     */
    public function verifyPendingDomains($domainId = null)
    {
        try {
            //verifica todos os dominios pendentes
            $domains = $this->getDomainModel()
                            ->with(['project', 'project.shopifyIntegrations'])
                            ->where('status', '!=', $this->getDomainModel()->getEnum('status', 'approved'));

            if (!empty($domainId)) {
                $domains->where('id', $domainId);
            }
            $domains->get();

            foreach ($domains as $domain) {

                if ($this->getCloudFlareService()
                         ->checkHtmlMetadata('https://checkout.' . $domain->name, 'checkout-cloudfox', '1')) {

                    $activated              = null;
                    $responseValidateDomain = null;
                    $responseValidateLink   = null;

                    $activated = true;
                    //$this->getCloudFlareService()->activationCheck($domain->name);

                    $linkBrandResponse = $this->getSendgridService()->getLinkBrand($domain->name);
                    $sendgridResponse  = $this->getSendgridService()->getZone($domain->name);

                    if (!empty($linkBrandResponse) && !empty($sendgridResponse)) {
                        $responseValidateDomain = $this->getSendgridService()->validateDomain($sendgridResponse->id);
                        $responseValidateLink   = $this->getSendgridService()
                                                       ->validateBrandLink($linkBrandResponse->id);
                    }

                    if ($activated && $responseValidateDomain && $responseValidateLink) {
                        $domain->update([
                                            'status' => $this->getDomainModel()->getEnum('status', 'approved'),
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
                                $htmlCart = $shopify->getTemplateHtml('sections/cart-template.liquid');

                                if ($htmlCart) {
                                    //template normal
                                    Log::warning('domains update command tema normal ...');

                                    $shopifyIntegration->update([
                                                                    'theme_type' => $this->getShopifyIntegrationModel()
                                                                                         ->getEnum('theme_type', 'basic_theme'),
                                                                    'theme_name' => $shopify->getThemeName(),
                                                                    'theme_file' => 'sections/cart-template.liquid',
                                                                    'theme_html' => $htmlCart,
                                                                ]);

                                    $shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart);
                                } else {
                                    Log::warning('domains update command tema ajax ...');

                                    //template ajax
                                    $shopifyIntegration->update([
                                                                    'theme_type' => $this->getShopifyIntegrationModel()
                                                                                         ->getEnum('theme_type', 'ajax_theme'),
                                                                    'theme_name' => $shopify->getThemeName(),
                                                                    'theme_file' => 'snippets/ajax-cart-template.liquid',
                                                                    'theme_html' => $htmlCart,
                                                                ]);

                                    $shopify->updateTemplateHtml('snippets/ajax-cart-template.liquid', $htmlCart, true);
                                }

                                //inserir o javascript para o trackeamento (src, utm)
                                $htmlBody = $shopify->getTemplateHtml('layout/theme.liquid');
                                if ($htmlBody) {
                                    //template do layout
                                    $shopifyIntegration->update([
                                                                    'layout_theme_html' => $htmlBody,
                                                                ]);

                                    //TODO validar para nao inserir duas vezes
                                    $shopify->insertUtmTracking('layout/theme.liquid', $htmlBody);
                                }
                            } catch (\Exception $e) {
                                //throwl

                            }
                        }
                    }

                    //integracao no shopify funcionou? aprova o dominio
                    $domain->update([
                                        'status' => $this->getDomainModel()->getEnum('status', 'approved'),
                                    ]);
                    Log::warning('domains update command final');
                } else {
                    $domain->update([
                                        'status' => $this->getDomainModel()->getEnum('status', 'pending'),
                                    ]);
                    Log::warning('domains update command final else');
                }
            }

            return true;
        } catch (Exception $e) {
            Log::warning('DomainService - Erro ao verificar dominios pendentes');
            report($e);

            return false;
        }
    }
}
