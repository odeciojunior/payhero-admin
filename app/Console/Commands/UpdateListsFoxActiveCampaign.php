<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Core\Services\ActiveCampaignService;

class UpdateListsFoxActiveCampaign extends Command
{
    protected $signature = 'command:UpdateListsFoxActiveCampaign';

    protected $description = 'Atualiza lista de contatos da CloudFox no ActiveCAmpaign';

    private $apiUrlFox = 'https://cloudfox.api-us1.com';
    // private $apiUrlFox = 'https://vpc1549909684.api-us1.com'; // teste

    private $apiKeyFox = 'd516ef3da2fed7a6b0fd033e4d692273419b539451bab1dd98748ea34fc61d7bfc79df05';

    // private $apiKeyFox = 'd8bbf54664a3839137192929f6a4947d654c6bac20e10d8fc12fb12aeab9be4cbabb6a2d'; // teste

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        try {
            // 2 - Usuários Ativos na plataforma
            $this->listActives(2);

            // 3 - Usuários que nunca venderam
            $this->listNoSales(3);

            // 4 - Usuários que vendem mais de 100k/mês
            $this->listUsers100k(4);

            // 6 - Usuários que não vendem a mais de 7 dias
            $this->listNoSalesMore7Days(6);

            // 14 - Usuários com saldo negativo na plataforma
            $this->listUsersNegativeBalance(14);

            // 15 - Usuários com chargeback superior a 1,5%
            $this->listUsersChargeback15(15);

            // 7 - Clientes com documentos faltando
            $this->listUsersNotApproved(7);
        } catch (Exception $e) {
            report($e);
        }

    }

    private function listUsers100k($listId)
    {
        try {
            $users = User::select('id', 'name', 'email', 'cellphone', 'cellphone_verified', 'email_verified',
                DB::raw('(SELECT SUM(total_paid_value) FROM sales WHERE sales.owner_id = users.id AND status = 1 AND created_at > NOW() - INTERVAL 30 DAY ) as total_value')
            )
                ->havingRaw('(SELECT SUM(total_paid_value) FROM sales WHERE sales.owner_id = users.id AND status = 1 AND created_at > NOW() - INTERVAL 30 DAY ) > 100000')
                ->get();

            $this->updateUsersList($users, $listId);
        } catch (Exception $e) {
            report($e);
        }
    }

    private function listActives($listId)
    {
        try {
            $users = User::whereHas('sales', function ($query) {
                $query->whereDate('created_at', '>', Carbon::now()->subdays(7)->todateTimeString());
            })->get();

            $this->updateUsersList($users, $listId);
        } catch (Exception $e) {
            report($e);
        }
    }

    private function listNoSales($listId)
    {
        try {
            /**  
             *  nova regra de negocio: bank_document_status, não é mais necessario estar aprovado para vender
            */

            $users = User::doesntHave('sales')->with('roles')->whereHas('roles', function ($query) {
                $query->where('name', 'account_owner');
            })->whereHas('companies', function ($query) {
                $query->where(function ($queryCompany) {
                    $queryCompany->where(function ($companyJuridical) {
                        $companyJuridical->where('address_document_status', Company::DOCUMENT_STATUS_APPROVED)
                            // ->where('bank_document_status', Company::DOCUMENT_STATUS_APPROVED)
                            ->where('contract_document_status', Company::DOCUMENT_STATUS_APPROVED)
                            ->where('company_type', Company::JURIDICAL_PERSON);
                    })
                        ->orWhere(function ($companyPhysical) {
                            $companyPhysical
                            // ->where('bank_document_status', Company::DOCUMENT_STATUS_APPROVED)
                            ->where('company_type', Company::PHYSICAL_PERSON);
                        });
                });
            })->where('address_document_status', User::DOCUMENT_STATUS_APPROVED)
                ->where('personal_document_status', User::DOCUMENT_STATUS_APPROVED)
                ->get();

            $this->updateUsersList($users, $listId);
        } catch (Exception $e) {
            report($e);
        }
    }

    private function listNoSalesMore7Days($listId)
    {
        try {
            $users = User::whereHas('sales', function ($query) {
                $query->whereDate('created_at', '<', Carbon::now()->subdays(7)->todateTimeString());
            })->whereDoesntHave('sales', function ($query) {
                $query->whereDate('created_at', '>', Carbon::now()->subdays(7)->todateTimeString());
            })->get();

            $this->updateUsersList($users, $listId);
        } catch (Exception $e) {
            report($e);
        }
    }

    private function listUsersNotApproved($listId)
    {
        try {
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'account_owner');
            })->whereDoesntHave('companies', function ($query) {
                $query->where(function ($queryCompany) {
                    $queryCompany->where(function ($companyJuridical) {
                        $companyJuridical->where('address_document_status', Company::DOCUMENT_STATUS_APPROVED)
                            // ->where('bank_document_status', Company::DOCUMENT_STATUS_APPROVED)
                            ->where('contract_document_status', Company::DOCUMENT_STATUS_APPROVED)
                            ->where('company_type', Company::JURIDICAL_PERSON);
                    })
                        ->orWhere(function ($companyPhysical) {
                            $companyPhysical
                            // ->where('bank_document_status', Company::DOCUMENT_STATUS_APPROVED)
                            ->where('company_type', Company::PHYSICAL_PERSON);
                        });
                });
            })->orWhere('address_document_status', '<>', User::DOCUMENT_STATUS_APPROVED)
                ->orWhere('personal_document_status', '<>', User::DOCUMENT_STATUS_APPROVED)
                ->get();

            $this->updateUsersList($users, $listId);
        } catch (Exception $e) {
            report($e);
        }
    }

    private function listUsersNegativeBalance($listId)
    {
        try {
            $users = User::select('id', 'name', 'email', 'cellphone', 'cellphone_verified', 'email_verified',
                DB::raw('(SELECT SUM(cielo_balance) FROM companies WHERE companies.user_id = users.id) as balance')
            )->havingRaw('(SELECT SUM(cielo_balance) FROM companies WHERE companies.user_id = users.id ) < 0')
                ->get();

            $this->updateUsersList($users, $listId);
        } catch (Exception $e) {
            report($e);
        }
    }

    private function listUsersChargeback15($listId)
    {
        try {
            $users = User::select('id', 'name', 'email', 'cellphone', 'cellphone_verified', 'email_verified',
                DB::raw('
                    ((SELECT COUNT(*) FROM sales WHERE sales.owner_id = users.id AND status = 4 ) /
                    (SELECT COUNT(*) FROM sales WHERE sales.owner_id = users.id AND status = 1 )) as tax_chergeback')
            )->havingRaw('
                    ((SELECT COUNT(*) FROM sales WHERE sales.owner_id = users.id AND status = 4 ) /
                    (SELECT COUNT(*) FROM sales WHERE sales.owner_id = users.id AND status = 1 )) > 0.015')
                ->get();

            $this->updateUsersList($users, $listId);
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
            $total = !empty($contacts['meta']['total']) ? (int)$contacts['meta']['total'] : 0;
            $pages = ($total > 0) ? ceil($total / 100) : 0;

            for ($i = 0; $i < $pages; $i++) {
                $contacts = $activeCampaignService->getContactsByList($listId, 100, ($i * 100));
                $contacts = json_decode($contacts, true);

                if (!empty($contacts['contacts'])) {
                    foreach ($contacts['contacts'] as $key => $value) {
                        $email = $value['email'];
                        $user = $users->where('email', $email);

                        if ($user->count() > 0) {
                            $keyItem = $user->keys()[0] ?? null;
                            $users->forget($keyItem);
                        } else {
                            $activeCampaignService->updateContactList($listId, $value['id'], 2);
                        }
                    }
                }
            }

            foreach ($users as $user) {
                $data = [
                    'firstName' => $user->name,
                    'phone' => $user->cellphone,
                    'email' => $user->email,
                    'lastName' => '',
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
