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
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

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

    private String $apiToken;
    private String $apiEndpoint;
    private int $gatewayId;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->gatewayId = Gateway::ASAAS_PRODUCTION_ID;
        $this->apiEndpoint = "https://www.asaas.com";

        if(!foxutils()->isProduction()){
            $this->gatewayId = Gateway::ASAAS_SANDBOX_ID;
            $this->apiEndpoint = "https://sandbox.asaas.com";
        }

        $configs = json_decode(foxutils()->xorEncrypt(Gateway::find($this->gatewayId)->json_config, "decrypt"), true);

        $this->apiToken = $configs['api_key'];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {

            $companies = Company::whereDoesntHave('gatewayCompanyCredential', function($q) {
                    $q->where('gateway_id', $this->gatewayId);
                    $q->whereNull('gateway_subseller_id');
                })
                ->where('contract_document_status', Company::STATUS_APPROVED)
                ->where('bank_document_status', Company::STATUS_APPROVED)
                ->where('address_document_status', Company::STATUS_APPROVED)
                ->get();

            $total = count($companies);

            $output = new ConsoleOutput();
            $progress = new ProgressBar($output, $total);
            $progress->start();

            foreach ($companies as $company) {

                $this->createAccount($company);
                $progress->advance();
            }

            $progress->finish();
            $output->writeln('Fim do command!!');
        }
        catch(Exception $ex) {
            report($ex->getMessage());
        }
    }

    public function createAccount(Company $company){

        try {

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
                $data['companyType'] = $this->getCompanyType($company);
                $data['personType'] = 'JURIDICA';
            } else {
                $data['personType'] = 'FISICA';
            }

            $url = "{$this->apiEndpoint}/api/v3/accounts";

            $result = $this->runCurl($url, 'POST', $data, $company->id);

            return $this->updateToReviewStatus($result);

        }
        catch(Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * @param $url
     * @param string $method
     * @param null $data
     * @return mixed
     * @throws Exception
     * @description GET/POST/PUT/DELETE
     */
    public function runCurl(string $url, string $method = 'GET', $data = null, $companyId = null)
    {
        try {

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            if ($method == 'POST') {
                curl_setopt($ch, CURLOPT_POST, 1);
            }
            if (in_array($method, ["POST", 'PUT'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_USERPWD, $this->apiToken);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'access_token: ' . $this->apiToken,
            ]);

            $result   = curl_exec($ch);
            $httpStatus     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $response = json_decode($result);

            if (($httpStatus < 200 || $httpStatus > 299) && (!isset($response->errors))) {
                report(new Exception('Erro na executação do Curl - Asaas Service' . $url . ' - code:' . $httpStatus));
            }

            $this->saveRequests($url, $response, $httpStatus, $data, $companyId);
            return $response;

        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }



    private function updateToReviewStatus($result): array
    {
        try {

            if(!empty($result->errors)) {
                throw new Exception("Empresa {$this->company->id} - {$this->company->fantasy_name}, erro: " . $result->errors[0]->description);
            }

            $gatewayCompanyCredential = new GatewaysCompaniesCredential();
            $dataToCreate = [
                'company_id' => $this->company->id,
                'gateway_id' => $this->gatewayId,
                'gateway_subseller_id' => $result->walletId,
                'gateway_api_key' => $result->apiKey,
            ];

            $gatewayCompanyCredential->create($dataToCreate);

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

    private function saveRequests($url, $result, $httpStatus, $data, $companyId)
    {
        AsaasBackofficeRequest::create(
            [
                'company_id' => $companyId,
                'sent_data' => json_encode(
                    [
                        'url' => $url,
                        'data' => $data
                    ]
                ),
                'response' => json_encode(
                    [
                        'result' => $result,
                        'status' => $httpStatus
                    ]
                )
            ]

        );
    }

    public function getCompanyType(Company $company)
    {
        $userDocument = foxutils()->onlyNumbers($company->user->document);

        if(str_contains($company->fantasy_name, 'LTDA')) {
            return 'LIMITED';
        }
        elseif(str_contains($company->fantasy_name, 'EIRELI')) {
            return 'INDIVIDUAL';
        }
        elseif(str_contains($company->fantasy_name, $userDocument)) {
            return 'MEI';
        }
        else {
            return 'INDIVIDUAL';
        }
    }
}
