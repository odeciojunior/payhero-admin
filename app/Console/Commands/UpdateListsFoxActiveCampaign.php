<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\ActiveCampaignService;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Invitation;
use Vinkla\Hashids\Facades\Hashids;

class UpdateListsFoxActiveCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:UpdateListsFoxActiveCampaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza lista de contatos da CloudFox no ActiveCAmpaign';

    /**
     * @var string
     */
    private $apiUrlFox = 'https://cloudfox.api-us1.com';
    // private $apiUrlFox = 'https://vpc1549909684.api-us1.com'; // teste

    /**
     * @var string
     */
    private $apiKeyFox = '2610ac50cd96c7af66367453fab94f2bfe3e5914c2a9d8d3979706825f73c06a769ae3e7';
    // private $apiKeyFox = 'd8bbf54664a3839137192929f6a4947d654c6bac20e10d8fc12fb12aeab9be4cbabb6a2d'; // teste

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

        // Listas
        // 1 - Solicitação de Convites pelo Site Principal
        // 2 - Usuários Ativos na plataforma (com vendas nos ultimos 7 dias)
        // 3 - Usuários que nunca venederam - sem vendas
        // 4 - Usuários que vendem mais de 100k/mês
        // 6 - Usuários que não vendem a mais de 7 dias
        $this->listInvites();
    }

    private function listInvites()
    {
        $listId = 1; // 1 - Solicitação de Convites pelo Site Principal
        $activeCampaignService = new ActiveCampaignService();
        $activeCampaignService->setAccess($this->apiUrlFox, $this->apiKeyFox, null);
        $contacts = $activeCampaignService->getContactsByList($listId, 1, 0);
        $contacts = json_decode($contacts, true);
        $total    = (int)$contacts['meta']['total'] ?? 0;

        $pages = ($total > 0) ? ceil($total/100) : 0;

        for ($i=0; $i < $pages; $i++) { 
            $contacts = $activeCampaignService->getContactsByList($listId, 100, ($i*100));
            $contacts = json_decode($contacts, true);

            foreach ($contacts['contacts'] as $key => $value) {
                $email = $value['email'];
                $user = User::where('email', $email)->first();
                if(isset($user->id)) {
                    // remove da lista
                    $activeCampaignService->updateContactList($listId, $value['id'], 2);
                } else {
                    // buscar convite por email com parameter > 15, se não existe:
                    $invite = Invitation::where('email_invited', $email)->first();
                    // dd(strlen($invite->parameter));
                    if(!isset($invite->id) || (isset($invite->id) && strlen($invite->parameter)) < 16) {
                        // criar convite
                        $invitation = Invitation::create([
                            'status'        => 2,
                            'email_invited' => $email
                        ]);
                        $parameter = Hashids::encode($invitation->id) . rand(10,999);
                        $invitation->update(['parameter' => $parameter ]);
                        // enviar url como campo customizado para activecp
                        // Link convite - id: 7
                        $activeCampaignService->setCustomFieldValue((int)$value['id'], 7, 'https://app.cloudfox.net/register/'.$parameter);
                    }
                }
            }
        }
    }

    private function addUsersList($users, $tagsAdd = null, $tagsDel = null, $listAdd = null, $listDel = null)
    {
        $activeCampaignService = new ActiveCampaignService();
        $activeCampaignService->setAccess($this->apiUrlFox, $this->apiKeyFox, null);

        foreach ($users as $user)
        {
            $data = [
                'firstName' => $user->name,
                'phone'     => $user->cellphone,
                'email'     => $user->email,
                'lastName'  => '',
            ];

            $event = ActivecampaignEvent::firstOrNew([
                'id'          => 0,
                'add_tags'    => (is_array($tagsAdd)) ? json_encode($tagsAdd) : null,
                'remove_tags' => (is_array($tagsDel)) ? json_encode($tagsDel) : null,
                'add_list'    => (is_array($listAdd)) ? json_encode($listAdd) : null,
                'remove_list' => (is_array($listDel)) ? json_encode($listDel) : null,
            ]);
            $activeCampaignService->sendContact($data, $event, null, null);
        }

    }
}
