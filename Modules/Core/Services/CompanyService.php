<?php

namespace Modules\Core\Services;

use Exception;
use LogicException;
use Modules\Core\Transformers\CompaniesSelectResource;
use Modules\Core\Transformers\CompanyResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Entities\PendingDebt;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Events\UpdateCompanyGetnetEvent;

/**
 * Class CompanyService
 * @package Modules\Core\Services
 */
class CompanyService
{
    public static function getSubsellerId(Company $company)
    {
        if (FoxUtils::isProduction()) {
            return $company->getGatewaySubsellerId(Gateway::GETNET_PRODUCTION_ID);
        }
        return $company->getGatewaySubsellerId(Gateway::GETNET_SANDBOX_ID);
    }

    public function getCompaniesUser($paginate = false)
    {
        try {
            $companyModel = new Company();
            $companies = $companyModel->with('user')->where('user_id', auth()->user()->account_owner_id);
            if ($paginate) {
                return CompanyResource::collection($companies->orderBy('order_priority')->paginate(10));
            }
            return CompaniesSelectResource::collection($companies->orderBy('order_priority')->get());
        } catch (Exception $e) {
            report($e);
            return [];
        }
    }

    public function haveAnyDocumentPending(): bool
    {
        $user = User::find(auth()->user()->account_owner_id);

        $check_PJ =  Company::where([
            'user_id' => $user->id,
            // 'bank_document_status' => Company::DOCUMENT_STATUS_APPROVED,
            'address_document_status' => Company::DOCUMENT_STATUS_APPROVED,
            'contract_document_status' => Company::DOCUMENT_STATUS_APPROVED,
        ])->exists();

        $check_PF = Company::where([
            'user_id' => $user->id,
            'document' => $user->document,
            // 'bank_document_status' => Company::DOCUMENT_STATUS_APPROVED,
        ])->exists();

        return $check_PJ || $check_PF;
    }

    public function getRefusedDocuments(int $companyId)
    {
        $companyModel = new Company();
        $company = $companyModel->with('companyDocuments')->find($companyId);
        $companyPresenter = $companyModel->present();
        $refusedDocuments = collect();
        if (!empty($company)) {
            foreach ($company->companyDocuments as $document) {
                if (!empty($document->refused_reason)) {
                    $dataDocument = [
                        'date' => $document->created_at->format('d/m/Y'),
                        'type_translated' => __(
                            'definitions.enum.company_document_type.' . $companyPresenter->getDocumentType(
                                $document->document_type_enum
                            )
                        ),
                        'document_url' => $document->document_url,
                        'refused_reason' => $document->refused_reason,
                    ];
                    $refusedDocuments->push(collect($dataDocument));
                }
            }
        }
        return $refusedDocuments;
    }

    public function verifyCnpj($cnpj): bool
    {
        $company = Company::where(
            [
                ['document', foxutils()->onlyNumbers($cnpj)],
                // ['bank_document_status', Company::DOCUMENT_STATUS_APPROVED],
                ['address_document_status', Company::DOCUMENT_STATUS_APPROVED],
                ['contract_document_status', Company::DOCUMENT_STATUS_APPROVED],
            ]
        )->first();
        if (!empty($company)) {
            return true;
        }
        return false;
    }

    public function getChangesUpdateBankData($company): bool
    {
        $companyChanges = $company->getChanges();
        if (
            (!empty($companyChanges['bank']) || array_key_exists('bank', $companyChanges)) ||
            (!empty($companyChanges['agency']) || array_key_exists('agency', $companyChanges)) ||
            (!empty($companyChanges['account']) || array_key_exists('account', $companyChanges)) ||
            (!empty($companyChanges['agency_digit']) || array_key_exists('agency_digit', $companyChanges)) ||
            (!empty($companyChanges['account_digit']) || array_key_exists('account_digit', $companyChanges))
        ) {
            $company->update(
                [
                    'bank_document_status' => Company::DOCUMENT_STATUS_PENDING,
                ]
            );
            return true;
        }
        return false;
    }

    public function getChangesUpdateCNPJ($company, $documentType)
    {
        if (!empty($documentType) && $documentType != $company->document) {
            $company->contract_document_status = Company::STATUS_PENDING;
        }
    }

    public function getCurrency(Company $company, $symbol = false)
    {
        $dolar = [
            'usa',
        ];
        $euro = [
            'portugal',
            'germany',
            'spain',
            'france',
            'italy',
        ];
        $real = [
            'brazil',
            'brasil',
        ];
        if (in_array($company->country, $dolar)) {
            return $symbol ? '$' : 'dolar';
        } elseif (in_array($company->country, $euro)) {
            return $symbol ? '€' : 'euro';
        } elseif (in_array($company->country, $real)) {
            return $symbol ? 'R$' : 'real';
        } else {
            return null;
        }
    }

    public function getNameCompanyByApiCNPJ($cnpj)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.receitaws.com.br/v1/cnpj/' . $cnpj);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response, true);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getCompanyByIdwallCNPJ($cnpj)
    {
        try {
            $idewallService = new IdwallService();
            $companyStatus = json_decode($idewallService->getGenerateProtocolByCNPJ($cnpj), true);
            $companyProtocol = $companyStatus['result']['numero'];
            /**
             * SLEEP É NECESSÁRIO PARA TER TEMPO DE PROCESSAR O RELATÓRIO
             */
            sleep(3);
            if (!empty($companyProtocol) && $companyStatus['status_code'] == 200) {
                $company = $idewallService->getReportByProtocolNumber($companyProtocol);
                return json_decode($company, true);
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function companyDocumentRefused()
    {
        $companies = Company::where('user_id', auth()->user()->account_owner_id)->where('active_flag', true)->get();
        foreach ($companies as $company) {
            if ($company->company_type == Company::JURIDICAL_PERSON){
                //$company->bank_document_status == Company::DOCUMENT_STATUS_REFUSED &&
                if( $company->address_document_status == Company::DOCUMENT_STATUS_REFUSED &&
                    $company->contract_document_status == Company::DOCUMENT_STATUS_REFUSED){
                    return $company;
                }
            } else{
                //if ($company->bank_document_status == Company::DOCUMENT_STATUS_REFUSED)
                return $company;
            }
        }
        return null;
    }

    public function companyDocumentApproved()
    {
        $companies = Company::where('user_id', auth()->user()->account_owner_id)->where('active_flag', true)->get();
        foreach ($companies as $company) {
            if ($company->company_type == Company::JURIDICAL_PERSON) {
                if (
                    //$company->bank_document_status == Company::DOCUMENT_STATUS_APPROVED ||
                    $company->address_document_status == Company::DOCUMENT_STATUS_APPROVED ||
                    $company->contract_document_status == Company::DOCUMENT_STATUS_APPROVED
                ) {
                    return $company;
                }
            } else{
                //if ($company->bank_document_status == Company::DOCUMENT_STATUS_APPROVED)
                return $company;
            }
        }
        return null;
    }

    public function verifyFieldsEmpty(Company $company): bool
    {
        if ($company->company_type == Company::JURIDICAL_PERSON) {
            if (empty($company->zip_code)) {
                return true;
            }
            if (empty($company->street)) {
                return true;
            }
            if (empty($company->neighborhood)) {
                return true;
            }
            if (empty($company->state)) {
                return true;
            }
            if (empty($company->city)) {
                return true;
            }
            if (empty($company->country)) {
                return true;
            }
        }
        if (empty($company->fantasy_name)) {
            return true;
        }
        if (empty($company->document)) {
            return true;
        }
        if (empty($company->bank)) {
            return true;
        }
        if (empty($company->agency)) {
            return true;
        }
        if (empty($company->account)) {
            return true;
        }
        return false;
    }

    public function createCompanyPjGetnet(Company $company)
    {
        try {
            if (empty($company->user->cellphone) || empty($company->user->email))
            {
                $this->createRowCredential($company->id);
                return;
            }

            $result = (new GetnetBackOfficeService())->createPjCompany($company);
            $this->updateToReviewStatusGetnet($company, $result);
        } catch (Exception $e) {
            report($e);
        }
    }

    private function updateToReviewStatusGetnet($company, $result)
    {
        $credential = GatewaysCompaniesCredential::where('company_id',$company->id)->first();

        if (empty($result) || empty(json_decode($result)->subseller_id)) {
            $credential->update(
                [
                    'gateway_status' => GatewaysCompaniesCredential::GATEWAY_STATUS_ERROR,
                ]
            );
            return;
        }
        $credential->update(
            [
                'gateway_subseller_id' => json_decode($result)->subseller_id,
                'gateway_status' => GatewaysCompaniesCredential::GATEWAY_STATUS_REVIEW,
            ]
        );
    }

    public function createCompanyPfGetnet(Company $company)
    {
        try {
            if ((new UserService())->verifyFieldsEmpty($company->user))
            {
                $this->createRowCredential($company->id);
                return;
            }

            $result = (new GetnetBackOfficeService())->createPfCompany($company);
            $this->updateToReviewStatusGetnet($company, $result);
        } catch (Exception $e) {
            report($e);
        }
    }

    // public function updateCompanyGetnet(Company $company): array
    // {
    //     $getnetService = new GetnetBackOfficeService();
    //     $user = $company->user;
    //     if ($company->company_type == Company::PHYSICAL_PERSON && (!(new UserService())->verifyFieldsEmpty($user)))
    //     {
    //         $getnetService->updatePfCompany($company);
    //     } elseif (!empty($user->cellphone) && !empty($user->email)) {
    //         $getnetService->updatePjCompany($company);
    //     }
    //     return [
    //         'message' => 'success',
    //         'data' => '',
    //     ];
    // }

    public function createRowCredential($companyId){
        return GatewaysCompaniesCredential::create([
            'company_id'=>$companyId,
            'gateway_id'=>Gateway::GETNET_PRODUCTION_ID,
            'gateway_status'=>GatewaysCompaniesCredential::GATEWAY_STATUS_PENDING
        ]);
    }

    public function updateCaptureTransactionEnabled(Company $company): void
    {
        try {
            $credential = $company->gatewayCredential(Gateway::GETNET_PRODUCTION_ID);
            if ($credential->gateway_status == GatewaysCompaniesCredential::GATEWAY_STATUS_APPROVED)
            {
                $credential->update(['capture_transaction_enabled' => true]);

                if ($this->isDocumentValidated($company->id) && (new UserService())->isDocumentValidated($company->user->id)) {
                    event(new UpdateCompanyGetnetEvent($company));
                }
                return ;
            }

            $credential->update(['capture_transaction_enabled' => false]);

        } catch (Exception $e) {
            report($e);
        }
    }

    public function isDocumentValidated(int $companyId): bool
    {
        $company = Company::find($companyId);
        if (!empty($company)) {
            if($company->company_type == Company::JURIDICAL_PERSON){
                return 
                    //$company->bank_document_status == Company::DOCUMENT_STATUS_APPROVED &&
                    $company->address_document_status == Company::DOCUMENT_STATUS_APPROVED &&
                    $company->contract_document_status == Company::DOCUMENT_STATUS_APPROVED;
            }
            return true; //$company->bank_document_status == Company::DOCUMENT_STATUS_APPROVED;
        }
        return false;
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

    public function getTax(int $gateway_release_money_days): float
    {
        switch ($gateway_release_money_days) {
            case 2:
                return Company::GATEWAY_TAX_2;
            case 15:
                return Company::GATEWAY_TAX_15;
            case 30:
                return Company::GATEWAY_TAX_30;
            default:
                return Company::GATEWAY_TAX_2;
        }
    }
}
