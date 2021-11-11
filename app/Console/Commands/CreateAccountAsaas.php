<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\AsaasBackofficeRequest;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\Gateways\CheckoutGateway;

class CreateAccountAsaas extends Command
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

        $this->gatewayId = foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID:Gateway::ASAAS_SANDBOX_ID;
        $this->api = new CheckoutGateway($this->gatewayId);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companies = Company::whereDoesntHave('gatewayCompanyCredential', function($q) {
                $q->where('gateway_id', $this->gatewayId);
            })
            ->where('contract_document_status', Company::STATUS_APPROVED)
            ->where('bank_document_status', Company::STATUS_APPROVED)
            ->where('address_document_status', Company::STATUS_APPROVED)
            ->get();

        foreach ($companies as $company) {
            $this->createAccount($company);
        }
    }

    public function createAccount(Company $company){
        try{

            $company->load('user');
            $company->user->email = $company->id . $company->user->email;
            $companyPhoneNumber = foxutils()->formatCellPhoneGetNet($company->user->cellphone ?? '5511988517040');

            $data = [
                "name" => $company->fantasy_name,
                "email" => foxutils()->isProduction(
                ) ? $company->user->email : 'cloudfox-teste' . $company->user->email,
                "cpfCnpj" => foxutils()->onlyNumbers($company->document),
                "phone" => foxutils()->isProduction(
                ) ? $companyPhoneNumber['dd'] . ' ' . $companyPhoneNumber['number'] : '55 995555555',
                "mobilePhone" => foxutils()->isProduction(
                ) ? $companyPhoneNumber['dd'] . ' ' . $companyPhoneNumber['number'] : '55 995555555',
                "address" => $company->street,
                "addressNumber" => $company->number,
                "complement" => $company->complement,
                "province" => $company->neighborhood,
                "postalCode" => $company->zip_code,
            ];

            if ($company->company_type == Company::JURIDICAL_PERSON) {
                $data['companyType'] = (new CompanyService)->getCompanyType($company);
                $data['personType'] = 'JURIDICA';
            } else {
                $data['personType'] = 'FISICA';
            }

            $result = $this->api->createAccount($data);

            return $this->updateToReviewStatus($result,$company);

        }
        catch(Exception $ex) {
            Log::info($ex->getMessage());
        }
    }

    private function updateToReviewStatus($result,$company): array
    {
        try {

            if($result->status =='error') {
                throw new Exception("Empresa {$company->id} - {$company->fantasy_name}, erro: " . $result->errors[0]->description);
            }

            $gatewayCompanyCredential = new GatewaysCompaniesCredential();
            $dataToCreate = [
                'company_id' => $company->id,
                'gateway_id' => $this->gatewayId,
                'gateway_subseller_id' => $result->walletId,
                'gateway_api_key' => $result->apiKey,
                'has_webhook'=>0
            ];

            $gatewayCompanyCredential->create($dataToCreate);

            $response = $this->api->registerWebhookTransferAsaas($gatewayCompanyCredential->company_id);
            if($response->status =='success'){
                $gatewayCompanyCredential->update(['has_webhook' => 1]);
            }

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
