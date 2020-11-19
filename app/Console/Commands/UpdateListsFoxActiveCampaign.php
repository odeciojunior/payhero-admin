<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\ActiveCampaignService;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Invitation;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;
use DB;

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
    private $apiKeyFox = 'd516ef3da2fed7a6b0fd033e4d692273419b539451bab1dd98748ea34fc61d7bfc79df05';
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
        try {
            // 2 - Usuários Ativos na plataforma
            $this->listActives();
            // 3 - Usuários que nunca venderam
            $this->listNoSales();
            // 4 - Usuários que vendem mais de 100k/mês
            $this->listUsers100k();
            // 6 - Usuários que não vendem a mais de 7 dias
            $this->listNoSalesMore7Days();
        } catch (Exception $e) {
            report($e);
        }
    }

    private function listUsers100k()
    {
        try {
            
            $users = User::select('id','name', 'email', 'cellphone', 'cellphone_verified', 'email_verified', 
                               DB::raw('(SELECT SUM(total_paid_value) FROM sales WHERE sales.owner_id = users.id AND status = 1 AND created_at > NOW() - INTERVAL 30 DAY ) as total_value')
                    )
                    ->havingRaw('(SELECT SUM(total_paid_value) FROM sales WHERE sales.owner_id = users.id AND status = 1 AND created_at > NOW() - INTERVAL 30 DAY ) > 100000')
                    ->get();

            $this->updateUsersList($users, 4); // 4 - Usuários que vendem mais de 100k/mês

        } catch (Exception $e) {
            report($e);
        }

    }
 
    private function listActives()
    {
        try {
            
            $users = User::whereHas('sales', function($query) {
                $query->whereDate('created_at', '>', Carbon::now()->subdays(30)->todateTimeString());
            })->get();

            $this->updateUsersList($users, 2);  // 2 - Usuários Ativos na plataforma

        } catch (Exception $e) {
            report($e);
        }
    }

    private function listNoSales()
    {
        try {
            
            $users = User::doesntHave('sales')->with('roles')->whereHas('roles', function($query) {
                $query->where('name', 'account_owner');
            })->get();
            
            $this->updateUsersList($users, 3); // 3 - Usuários que nunca venderam

        } catch (Exception $e) {
            report($e);
        }
    }

    private function listNoSalesMore7Days()
    {
        try {

            $users = User::whereHas('sales', function($query) {
                $query->whereDate('created_at', '<', Carbon::now()->subdays(7)->todateTimeString());
            })->whereDoesntHave('sales', function($query) {
                $query->whereDate('created_at', '>', Carbon::now()->subdays(7)->todateTimeString());
            })->get();

            $this->updateUsersList($users, 6); // 6 - Usuários que não vendem a mais de 7 dias

        } catch (Exception $e) {
            report($e);
        }
    }

    private function updateUsersList($users, $listId)
    {
        try {

            $activeCampaignService = new ActiveCampaignService();
            $activeCampaignService->setAccess($this->apiUrlFox, $this->apiKeyFox, null);
            $contacts = $activeCampaignService->getContactsByList($listId, 1, 0);
            $contacts = json_decode($contacts, true);
            $total    = !empty($contacts['meta']['total']) ? (int)$contacts['meta']['total'] : 0;
            $pages = ($total > 0) ? ceil($total/100) : 0;

            for ($i=0; $i < $pages; $i++) { 

                $contacts = $activeCampaignService->getContactsByList($listId, 100, ($i*100));
                $contacts = json_decode($contacts, true);

                foreach ($contacts['contacts'] as $key => $value) {
                    $email = $value['email'];
                    $user = $users->where('email', $email);

                    if($user->count() > 0) {
                        $keyItem = $user->keys()[0] ?? null;
                        $users->forget($keyItem);
                    } else {
                        $activeCampaignService->updateContactList($listId, $value['id'], 2);
                    }
                }
            }

            foreach ($users as $user)
            {
                $data = [
                    'firstName' => $user->name,
                    'phone'     => $user->cellphone,
                    'email'     => $user->email,
                    'lastName'  => '',
                ];

                $contact = $activeCampaignService->createOrUpdateContact($data);
                $contact = json_decode($contact, true);
                if (isset($contact['contact']['id'])) {
                    $activeCampaignService->updateContactList($listId, $contact['contact']['id'], 1);
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
