<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Services\ShopifyService;

class GenericCommand extends Command
{
    protected $signature = 'generic {user?}';

    protected $description = 'Command description';

    public function handle()
    {
        $integrations = ShopifyIntegration::with('project')
            ->where('theme_name', 'Debut')
            ->orderByDesc('id')
            ->get();

        $count = 1;
        $total = $integrations->count();
        foreach ($integrations as $integration) {
            $this->line("Loja {$count} de {$total}: $integration->url_store");
            try {
                $value = "{% comment %}\n  The contents of the cart.liquid template can be found in /sections/cart-template.liquid\n{% endcomment %}\n{% section 'cart-template' %}\n";
                $shopify = new ShopifyService($integration->url_store, $integration->token);
                $shopify->setThemeByRole('main');
                $htmlCart = $shopify->getTemplateHtml($shopify::templateKeyNames[1]);
                if (empty($htmlCart)) {
                    $shopify->setTemplateHtml($shopify::templateKeyNames[1], $value);
                }
                if($integration->skip_to_cart) {
                    $this->setSkipToCart($shopify, $integration,false);
                    $this->setSkipToCart($shopify, $integration, true);
                }
                $this->line("foi!");
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
            $count++;
        }
    }

    private function setSkipToCart($shopify, $integration, $isSet = false)
    {
        $shopify->setSkipToCart($isSet);

        $shopify->setThemeByRole('main');

        $htmlCart = null;
        $templateKeyName = null;
        foreach ($shopify::templateKeyNames as $template) {
            $templateKeyName = $template;
            $htmlCart = $shopify->getTemplateHtml($template);
            if ($htmlCart) break;
        }

        $domain = $integration->project->domains->first();
        $domainName = $domain ? $domain->name : null;

        $shopify->updateTemplateHtml($templateKeyName, $htmlCart, $domainName);
    }
}


