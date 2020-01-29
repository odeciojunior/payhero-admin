<?php

namespace App\Console\Commands;

use DB;
use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Log;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\ActivecampaignCustom;
use Modules\Core\Services\ActiveCampaignService;
use Modules\Core\Entities\ActivecampaignIntegration;

class CustomFieldsActiveCampaign extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'CustomFieldsActiveCampaign'; // passa id para chamar quando cria nova integração
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Cria campos customizados nas integrações Active Campaign';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {

        $sales = Sale::whereDate('created_at', '>=', '2020-01-20 15:00:00.0')->whereNotNull('shopify_order')
                     ->whereDate('created_at', '<=', '2020-01-22 17:00:00.0');

        foreach ($sales->cursor() as $sale) {
            try {

                $shopifyIntegration = ShopifyIntegration::where('project_id', $sale->project_id)->first();

                if (!empty($shopifyIntegration)) {

                    $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
                    $sh             = $shopifyService->updateOrder($sale);
                }
            } catch (Exception $e) {
                print_r('Erro na venda: ' . $sale->id);
            }
        }

        dd('Terminou!');

        // $integrations = ActivecampaignIntegration::with('customFields')->get();

        // $fieldsDefault = [
        //     'url_boleto',
        //     'projeto_nome',
        //     'link_carrinho_abandonado',
        //     'codigo_pedido',
        //     'produtos',
        //     'sub_total',
        //     'frete',
        //     'codigo_rastreio',
        //     'link_rastreamento',
        // ];

        // $service = new ActiveCampaignService();
        // foreach ($integrations as $integration) {

        //     $service->setAccess($integration->api_url, $integration->api_key, $integration->id);

        //     $fieldsCreate = $fieldsDefault;

        //     $customFieldsIntegration = $integration->customFields->pluck('custom_field')->toArray();

        //     if(isset($customFieldsIntegration[0])) {
        //         $fieldsCreate = array_diff($fieldsDefault, $customFieldsIntegration);
        //     }

        //     $customnFieldsActive = $service->getCustomFields();
        //     if(isset($customnFieldsActive['fields'])) {

        //         foreach ($customnFieldsActive['fields'] as $value) {
        //             if(in_array($value['title'], $fieldsCreate)) {
        //                 $fieldsCreate = array_diff($fieldsCreate, [$value['title']]);
        //                 ActivecampaignCustom::create([
        //                     'custom_field'                  => $value['title'],
        //                     'custom_field_id'               => $value['id'],
        //                     'activecampaign_integration_id' =>$integration->id
        //                 ]);
        //             }
        //         }
        //     }

        //     foreach ($fieldsCreate as $value) {
        //         $type = ($value == 'produtos') ? 'listbox' : 'text';
        //         $newField = json_decode($service->createCustomField($value, $type), true);
        //         if(isset($newField['field']['id'])) {

        //             ActivecampaignCustom::create([
        //                 'custom_field'                  => $value,
        //                 'custom_field_id'               => $newField['field']['id'],
        //                 'activecampaign_integration_id' =>$integration->id
        //             ]);

        //             $service->createCustomFieldRelation($newField['field']['id'], 0);
        //         }
        //     }
        // }
    }
}
