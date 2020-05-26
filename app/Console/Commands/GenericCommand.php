<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Core\Entities\AnticipatedTransaction;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\ShopifyService;
use Slince\Shopify\Client;
use Slince\Shopify\PublicAppCredential;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class GenericCommand
 * @package App\Console\Commands
 */
class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'generic';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        $this->shopifyOrders();

        dd('feito');

        $cloudflareService = new CloudFlareService();

        $domains = Domain::all();
        $total = $domains->count();

        foreach ($domains as $key => $domain) {
            $this->info($key + 1 . ' de ' . $total . '. Domínio: ' . $domain->name);
            try {

                //tracking

                $records = $cloudflareService->getRecords($domain->name);
                $domainRecord = collect($records)->first(function ($item) {
                    if (Str::contains($item->name, 'affiliate.')) {
                        return $item;
                    }
                });
                if(empty($domainRecord)){
                    $this->warn('Record não encontrado');
                    continue;
                }

                $deleted = $cloudflareService->deleteRecord($domainRecord->id);

                if ($deleted) {
                    $this->line('Record A affiliate deletado!');
                    $recordId = $cloudflareService->addRecord("CNAME", 'affiliate', 'cloudfoxsuit-checkout-balance-1912358215.us-east-1.elb.amazonaws.com');
                    $this->line('Record CNAME affiliate criado: ' . $recordId);
                }
                else{
                    $this->line('Erro ao atualizar record!');
                }


                //tracking

                $records = $cloudflareService->getRecords($domain->name);
                $domainRecord = collect($records)->first(function ($item) {
                    if (Str::contains($item->name, 'tracking.')) {
                        return $item;
                    }
                });
                if(empty($domainRecord)){
                    $this->warn('Record não encontrado');
                    continue;
                }

                $deleted = $cloudflareService->deleteRecord($domainRecord->id);

                if ($deleted) {
                    $this->line('Record A tracking deletado!');
                    $recordId = $cloudflareService->addRecord("CNAME", 'tracking', 'cloudfoxsuit-admin-balance-942137392.us-east-1.elb.amazonaws.com');
                    $this->line('Record CNAME tracking criado: ' . $recordId);
                }
                else{
                    $this->line('Erro ao atualizar record!');
                }

            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        $this->info('ACABOOOOOOOOOOOOOU!');

    }

    public function shopifyOrders(){

        $saleModel     = new Sale();
        $salePresenter = $saleModel->present();
        $date       = Carbon::now()->subDay()->toDateString();
        $sales         = $saleModel->whereNull('shopify_order')
                                   ->whereIn('status',
                                             [
                                                 $salePresenter->getStatus('approved'),
                                                 $salePresenter->getStatus('pending'),
                                             ])
                                   ->whereDate('created_at', '>',$date)
                                   ->whereHas('project.shopifyIntegrations', function($query) {
                                       $query->where('status', 2);
                                   })
                                   ->get();

        foreach ($sales as $sale) {
            try {
                $shopifyIntegration = ShopifyIntegration::where('project_id', $sale->project_id)->first();
                $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
                $shopifyService->newOrder($sale);
                $this->line('sucesso');
            } catch (Exception $e) {
                $this->line('erro -> ' . $e->getMessage());
            }
        }
    }

}


