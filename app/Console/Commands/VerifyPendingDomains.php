<?php

namespace App\Console\Commands;

use App\Entities\Domain;
use App\Entities\ShopifyIntegration;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\ShopifyService;

class VerifyPendingDomains extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'verify:pendingdomains';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Verifica se os domínios pendentes ficaram ativos';
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
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        try {
            $domains = $this->getDomainModel()
                            ->with(['project', 'project.shopifyIntegrations'])
                            ->where('status', '!=', $this->getDomainModel()->getEnum('status', 'approved'))
                            ->get();

            foreach ($domains as $domain) {

                $activated              = null;
                $responseValidateDomain = null;
                $responseValidateLink   = null;

                $activated = $this->getCloudFlareService()->activationCheck($domain->name);

                $linkBrandResponse = $this->getSendgridService()->getLinkBrand($domain->name);
                $sendgridResponse  = $this->getSendgridService()->getZone($domain->name);

                if (!empty($linkBrandResponse) && !empty($sendgridResponse)) {
                    $responseValidateDomain = $this->getSendgridService()->validateDomain($sendgridResponse->id);
                    $responseValidateLink   = $this->getSendgridService()->validateBrandLink($linkBrandResponse->id);
                }

                if ($activated && $responseValidateDomain && $responseValidateLink) {
                    $domain->update([
                                        'status' => $this->getDomainModel()->getEnum('status', 'approved'),
                                    ]);
                }

                if ($this->getCloudFlareService()
                         ->checkHtmlMetadata('https://checkout.' . $domain->name, 'checkout-cloudfox', '1')) {

                    if (!empty($domain->project->shopify_id)) {
                        //dominio shopify, fazer as alteracoes nos templates
                        foreach ($domain->project->shopifyIntegrations as $shopifyIntegration) {

                            try {

                                $shopify = $this->getShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                                $shopify->setThemeByRole('main');
                                $htmlCart = $shopify->getTemplateHtml('sections/cart-template.liquid');

                                if ($htmlCart) {
                                    //template normal

                                    $shopifyIntegration->update([
                                                                    'theme_type' => $this->getShopifyIntegrationModel()
                                                                                         ->getEnum('theme_type', 'basic_theme'),
                                                                    'theme_name' => $shopify->getThemeName(),
                                                                    'theme_file' => 'sections/cart-template.liquid',
                                                                    'theme_html' => $htmlCart,
                                                                ]);

                                    $shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart);
                                } else {
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
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro no command VerifyPendingDomains - ');
            report($e);
        }
    }
}
