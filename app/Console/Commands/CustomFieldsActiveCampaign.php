<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\ActiveCampaignService;
use Modules\Core\Entities\User;
use Modules\Core\Entities\ActivecampaignIntegration;
use Modules\Core\Entities\ActivecampaignCustom;
use Modules\Core\Entities\Log;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;
use DB;

class CustomFieldsActiveCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CustomFieldsActiveCampaign'; // passa id para chamar quando cria nova integração

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria campos customizados nas integrações Active Campaign';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $integrations = ActivecampaignIntegration::with('customFields')->where('id', 6)->get();

        $fieldsDefault = [
            'url_boleto',
            'projeto_nome',
            'link_carrinho_abandonado',
            'codigo_pedido',
            'produtos',
            'sub_total',
            'frete',
            'codigo_rastreio',
            'link_rastreamento',
        ];

        $service = new ActiveCampaignService();
        foreach ($integrations as $integration) {
            
            $service->setAccess($integration->api_url, $integration->api_key, $integration->id);

            $fieldsCreate = $fieldsDefault;

            $customFieldsIntegration = $integration->customFields->pluck('custom_field')->toArray();

            if(isset($customFieldsIntegration[0])) {
                $fieldsCreate = array_diff($fieldsDefault, $customFieldsIntegration);
            }

            $customnFieldsActive = $service->getCustomFields();
            if(isset($customnFieldsActive['fields'])) {

                foreach ($customnFieldsActive['fields'] as $value) {
                    if(in_array($value['title'], $fieldsCreate)) {
                        $fieldsCreate = array_diff($fieldsCreate, [$value['title']]);
                        ActivecampaignCustom::create([
                            'custom_field'                  => $value['title'],
                            'custom_field_id'               => $value['id'],
                            'activecampaign_integration_id' =>$integration->id
                        ]);
                    }
                }
            }

            foreach ($fieldsCreate as $value) {
                $type = ($value == 'produtos') ? 'listbox' : 'text';
                $newField = json_decode($service->createCustomField($value, $type), true);
                if(isset($newField['field']['id'])) {

                    ActivecampaignCustom::create([
                        'custom_field'                  => $value,
                        'custom_field_id'               => $newField['field']['id'],
                        'activecampaign_integration_id' =>$integration->id
                    ]);

                    $service->createCustomFieldRelation($newField['field']['id'], 0);
                }
            }
        }
    }
}
