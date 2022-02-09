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
    protected $signature = 'createAccountAsaas';

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
        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            $companies = Company::
            whereHas('gatewayCompanyCredential', function($q) {
                $q->where('gateway_id', $this->gatewayId);
                $q->where('gateway_status', '!=', GatewaysCompaniesCredential::GATEWAY_STATUS_APPROVED);
            })
                ->where('contract_document_status', Company::STATUS_APPROVED)
                ->where('bank_document_status', Company::STATUS_APPROVED)
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

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }

    public function createAccount(Company $company){
        try{

            $company->load('user');
            $companyPhoneNumber = foxutils()->formatCellPhoneGetNet($company->user->cellphone ?? '5511988517040');

            $data = [
                'company_id' => Hashids::encode($company->id),
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
                "BankData" => [
                    "Bank" => [
                        "Code" => $company->bank,
                    ],
                    "AccountType" => [
                        "Code" => "CC",
                    ],
                    "BankAgency" => $company->agency,
                    "BankAgencyDigit" => $company->agency_digit,
                    "BankAccount" => $company->account,
                    "BankAccountDigit" => $company->account_digit
                ],
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
//                "MerchantSplit" => [
//                    [
//                        "PaymentMethodCode" => "1", // 1 - Boleto bancário
//                        "IsSubaccountTaxPayer" => false, // Informar se a taxa de custo será paga pela subconta
//                        "Taxes" => [
//                            [
//                                "TaxTypeName" => "1", // Tipo da taxa, informar '1' para Percentual ou '2' para Valor
//                                "Tax" => "1.00" // Informar o valor da taxa, em percentual ou valor.
//                            ],
//                        ],
//                    ],
//                    [
//                        "PaymentMethodCode" => "2", // 2 - Cartão de crédito
//                        "IsSubaccountTaxPayer" => false,
//                        "Taxes" => [
//                            [
//                                "TaxTypeName" => "1",
//                                "Tax" => "1.00"
//                            ],
//                        ],
//                    ],
//                    [
//                        "PaymentMethodCode" => "3", // 3 - Criptomoedas
//                        "IsSubaccountTaxPayer" => false,
//                        "Taxes" => [
//                            [
//                                "TaxTypeName" => "1",
//                                "Tax" => "1.00"
//                            ],
//                        ],
//                    ],
//                    [
//                        "PaymentMethodCode" => "4", // 4 - Cartão de débito
//                        "IsSubaccountTaxPayer" => false,
//                        "Taxes" => [
//                            [
//                                "TaxTypeName" => "1",
//                                "Tax" => "1.00"
//                            ],
//                        ],
//                    ],
//                    [
//                        "PaymentMethodCode" => "6", // 6 - Pix
//                        "IsSubaccountTaxPayer" => false,
//                        "Taxes" => [
//                            [
//                                "TaxTypeName" => "1",
//                                "Tax" => "1.00"
//                            ],
//                        ],
//                    ],
//                ],
            ];

            //dd($data);

            $result = $this->api->createAccount($data);

            return $this->updateToReviewStatus($result,$company);

        }
        catch(Exception $ex) {
            Log::info($ex->getMessage());
        }
    }


    private function updateToReviewStatus($result,$company): array
    {
        //Log::info(print_r($result));
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
                'gateway_subseller_id' => $result->response->ResponseDetail->Token,
                'gateway_api_key' => $result->response->ResponseDetail->SecretKey
            ]);

            $gatewayCompanyCredentialSend->update([
                'gateway_status' => GatewaysCompaniesCredential::GATEWAY_STATUS_APPROVED,
                'gateway_subseller_id' => $result->response->ResponseDetail->TokenSandbox,
                'gateway_api_key' => $result->response->ResponseDetail->SecretKeySandbox
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

}
