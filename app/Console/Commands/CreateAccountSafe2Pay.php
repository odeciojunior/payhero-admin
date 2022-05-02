<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Services\Gateways\CheckoutGateway;
use Vinkla\Hashids\Facades\Hashids;

class CreateAccountSafe2Pay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'createAccountSafe2Pay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $api = null;
    private int $gatewayId;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->gatewayId = Gateway::SAFE2PAY_PRODUCTION_ID; // foxutils()->isProduction() ? Gateway::SAFE2PAY_PRODUCTION_ID : Gateway::SAFE2PAY_SANDBOX_ID;
        $this->api = new CheckoutGateway($this->gatewayId);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        try {

            $companies = Company::where('contract_document_status', Company::STATUS_APPROVED)
                //->where('bank_document_status', Company::STATUS_APPROVED)
                ->where('address_document_status', Company::STATUS_APPROVED)
                ->get();

            $total = count($companies);
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            foreach ($companies as $company) {
                $this->line('  Company: ' . $company->id . ' CompanyName: ' . $company->fantasy_name);

                $this->createAccount($company);
                $bar->advance();
            }

            $bar->finish();

        } catch (Exception $e) {
            report($e);
        }

    }

    public function createAccount(Company $company){
        try{

            $company->load('user');
            $companyPhoneNumber = foxutils()->formatCellPhoneGetNet($company->user->cellphone ?? '5511988517040');

            $data = [
                "Name" => $company->fantasy_name,
                "CommercialName" => $company->fantasy_name,
                "Identity" => foxutils()->onlyNumbers($company->document),
                "ResponsibleName" => $company->user->name,
                "ResponsibleIdentity" => foxutils()->onlyNumbers($company->user->document),
                "ResponsiblePhone" => $companyPhoneNumber['dd'] . ' ' . $companyPhoneNumber['number'] ,
                "Email" => $company->user->email,
                "TechName" => "CloudFox",
                "TechIdentity" => "02901053076",
                "TechEmail" => "julioleichtweis@cloudfox.net",
                "TechPhone" => "55996931098",
                'IsPanelRestricted' => true,                
                "Address" => [
                    "ZipCode" => $company->zip_code,
                    "Street" => $company->street,
                    "Number" => $company->number,
                    "Complement" => $company->complement,
                    "District" => $company->neighborhood,
                    "CityName" => $company->city,
                    "StateInitials" => $company->state,
                    "CountryName" => "Brasil",
                ],
            ];

            $bankAccounts = $company->getDefaultBankAccount();
            if(!empty($bankAccounts) && $bankAccounts->transfer_type=='TED'){
                $data['BankData'] = [ 
                    "Bank" => [
                        "Code" => $bankAccounts->bank,
                    ],
                    "AccountType" => [
                        "Code" => "CC",
                    ],
                    "BankAgency" => $bankAccounts->agency,
                    "BankAgencyDigit" => $bankAccounts->agency_digit,
                    "BankAccount" => $bankAccounts->account,
                    "BankAccountDigit" => $bankAccounts->account_digit
                ];
            }

            $data['variables']['company_id'] = Hashids::encode($company->id);

            $createRowCredentialProd = $this->createRowCredential($company->id, Gateway::SAFE2PAY_PRODUCTION_ID);
            $createRowCredentialSendbox = $this->createRowCredential($company->id, Gateway::SAFE2PAY_SANDBOX_ID);

            if ($createRowCredentialProd || !$this->company->getGatewaySubsellerId($this->gatewayId)){
                $result = $this->api->createAccount($data);
            } else {
                $data['variables']['gateway_subseller_id'] = Hashids::encode($this->company->getGatewaySubsellerId($this->gatewayId));
                $result = $this->api->updateAccount($data);
            }

            return $this->updateToReviewStatus($result,$company);

        }
        catch(Exception $ex) {
            Log::info($ex->getMessage());
        }
    }


    private function updateToReviewStatus($result,$company): array
    {

        try {

            $gatewayCompanyCredentialProd = GatewaysCompaniesCredential::where('company_id', $company->id)->where('gateway_id', Gateway::SAFE2PAY_PRODUCTION_ID)->first();
            $gatewayCompanyCredentialSend = GatewaysCompaniesCredential::where('company_id', $company->id)->where('gateway_id', Gateway::SAFE2PAY_SANDBOX_ID)->first();

            if($result->status == 'error') {

                $gatewayCompanyCredentialProd->update([
                    'gateway_status' => GatewaysCompaniesCredential::GATEWAY_STATUS_ERROR
                ]);
                $gatewayCompanyCredentialSend->update([
                    'gateway_status' => GatewaysCompaniesCredential::GATEWAY_STATUS_ERROR
                ]);

                throw new Exception("Empresa {$company->id} - {$company->fantasy_name}, erro: " . json_encode($result));
            }


            $gatewayCompanyCredentialProd->update([
                'gateway_status' => GatewaysCompaniesCredential::GATEWAY_STATUS_APPROVED,
                'gateway_subseller_id' => $result->gateway_subseller_id,
                'gateway_api_key' => $result->gateway_api_key
            ]);

            $gatewayCompanyCredentialSend->update([
                'gateway_status' => GatewaysCompaniesCredential::GATEWAY_STATUS_APPROVED,
                'gateway_subseller_id' => $result->gateway_subseller_id,
                'gateway_api_key' => $result->gateway_api_key_sandbox
            ]);

            return [
                'message' => 'Cadastro realizado com sucesso',
                'success' => true
            ];
        } catch (Exception $e) {
            report($e);

            return [
                'message' => 'Ocorreu um erro ao tentar realizar cadastro no assas',
                'success' => false
            ];
        }
    }

    public function createRowCredential($companyId, $gatewayId)
    {
        $gatewaysCompaniesCredential = new GatewaysCompaniesCredential();

        if(!($gatewaysCompaniesCredential::where('company_id', $companyId)
            ->where('gateway_id', $gatewayId)
            ->exists())
        ) {
            GatewaysCompaniesCredential::create([
                'company_id'=>$companyId,
                'gateway_id'=>$gatewayId,
                'gateway_status' => GatewaysCompaniesCredential::GATEWAY_STATUS_PENDING,
            ]);
            return true;
        }
        return false;
    }
}
