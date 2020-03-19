<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\CloudFlareService;

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

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function handle()
    {
        // dd($this->calInterestTotalValue());

        $cloudflareService = new CloudFlareService();

        $domains = $cloudflareService->getZones();

        $total = count($domains);

        foreach ($domains as $key => $domain) {

            $this->info($key + 1 . ' de ' . $total . '. Domínio: ' . $domain->name);

            try {
                $records = $cloudflareService->getRecords($domain->name);
                $checkoutRecord = collect($records)->first(function ($item) {
                    if (Str::contains($item->name, 'affiliate.')) {
                        return $item;
                    }
                });

                if (isset($checkoutRecord)) {
                    $deleted = $cloudflareService->deleteRecord($checkoutRecord->id);
                    if ($deleted) {
                        $this->line('Record antigo deletado!');
                        $recordId = $cloudflareService->addRecord("A", 'affiliate', $cloudflareService::affiliateIp);
                        $this->line('Novo record criado: ' . $recordId);
                    }
                } else {
                    $this->warn('Record não encontrado');
                    $recordId = $cloudflareService->addRecord("A", 'affiliate', $cloudflareService::affiliateIp);
                    $this->line('Novo record criado: ' . $recordId);
                }
            } catch (\Exception $e) {

                $this->error($e->getMessage());
            }
        }
        $this->info('ACABOOOOOOOOOOOOOU!');
    }

    public function calInterestTotalValue()
    {
        $sales = Sale::whereNull('interest_total_value')
                     ->where('payment_method', 1)
                     ->get();

        $arrayS = [];
        $count = 0;
        foreach ($sales as $sale) {
            
            $shopifyDiscount   = (!is_null($sale->shopify_discount)) ? intval(preg_replace("/[^0-9]/", "", $sale->shopify_discount)) : 0; // varchar

            $subTotal          = intval(strval($sale->sub_total * 100)); // decimal

            $shipmentValue    = intval(strval($sale->shipment_value * 100)); // decimal

            $automaticDiscount = intval($sale->automatic_discount); // int

            $totalPaidValue    = intval(strval($sale->total_paid_value * 100)); // decimal

            $interesetTotalValue = $totalPaidValue - (($subTotal + $shipmentValue) - $shopifyDiscount - $automaticDiscount);

            if($interesetTotalValue >= 0) {
                $s = $sale->update(['interest_total_value' => $interesetTotalValue]);
            } else {
                $s = false;
            }

            if($s == true) {
                $count ++;
            } else {
                $arrayS[] = $sale->id;
            }
        }

        dd($arrayS, $count);
    }
}
