<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Companies\Transformers\CompanyResource;
use Modules\Core\Entities\AnticipatedTransaction;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Tracking;
use Illuminate\Support\Facades\DB;
use LogicException;
use Modules\Core\Entities\User;
use Modules\Core\Events\UpdateCompanyGetnetEvent;

/**
 * Class CompaniesService
 * @package Modules\Core\Services
 */
class CompanyService
{

    const STATEMENT_AUTOMATIC_LIQUIDATION_TYPE = 1;
    const STATEMENT_MANUAL_LIQUIDATION_TYPE = 2;

    public function getCompaniesUser($paginate = false)
    {
        try {
            $companyModel = new Company();

            $companies = $companyModel->with('user')->where('user_id', auth()->user()->account_owner_id);

            if ($paginate) {
                return CompanyResource::collection($companies->orderBy('order_priority')->paginate(10));
            } else {
                return CompaniesSelectResource::collection($companies->orderBy('order_priority')->get());
            }
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    public function isDocumentValidated(int $companyId)
    {
        $companyModel = new Company();
        $company = $companyModel->find($companyId);
        $companyPresenter = $companyModel->present();
        if (!empty($company)) {
            if ($company->company_type == $companyPresenter->getCompanyType('juridical person')) {
                if ($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved') &&
                    $company->address_document_status == $companyPresenter->getAddressDocumentStatus('approved') &&
                    $company->contract_document_status == $companyPresenter->getContractDocumentStatus('approved')) {
                    return true;
                }
            } else {
                if ($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved')) {
                    return true;
                }
            }
        }

        return false;
    }

    public function haveAnyDocumentPending()
    {
        $companyModel = new Company();
        $companies = $companyModel->where('user_id', auth()->user()->account_owner_id)->get();
        $companyPresenter = $companyModel->present();

        foreach ($companies as $company) {
            if ($company->company_type == $companyPresenter->getCompanyType('juridical person')) {
                if (($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved') ||
                        $company->bank_document_status == $companyPresenter->getBankDocumentStatus('analyzing')) &&
                    ($company->address_document_status == $companyPresenter->getAddressDocumentStatus('approved') ||
                        $company->address_document_status == $companyPresenter->getAddressDocumentStatus(
                            'analyzing'
                        )) &&
                    ($company->contract_document_status == $companyPresenter->getContractDocumentStatus('approved') ||
                        $company->contract_document_status == $companyPresenter->getContractDocumentStatus(
                            'analyzing'
                        ))) {
                    return false;
                }
            } else {
                if ($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved') ||
                    $company->bank_document_status == $companyPresenter->getBankDocumentStatus('analyzing')) {
                    return false;
                }
            }
        }

        return true;
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
                            'definitions.enum.company_document_type.'.$companyPresenter->getDocumentType(
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

    public function verifyCnpj($cnpj)
    {
        $companyModel = new Company();
        $companyPresenter = $companyModel->present();
        $cnpj = preg_replace("/[^0-9]/", "", $cnpj);
        $company = $companyModel->where(
            [
                ['document', $cnpj],
                ['bank_document_status', $companyPresenter->getBankDocumentStatus('approved')],
                ['address_document_status', $companyPresenter->getAddressDocumentStatus('approved')],
                ['contract_document_status', $companyPresenter->getContractDocumentStatus('approved')],
            ]
        )->first();
        if (!empty($company)) {
            return true;
        } else {
            return false;
        }
    }

    public function getChangesUpdateBankData($company)
    {
        $companyChanges = $company->getChanges();

        if ((!empty($companyChanges['bank']) || array_key_exists('bank', $companyChanges))
            || (!empty($companyChanges['agency']) || array_key_exists('agency', $companyChanges))
            || (!empty($companyChanges['account']) || array_key_exists('account', $companyChanges))
            || (!empty($companyChanges['agency_digit']) || array_key_exists('agency_digit', $companyChanges))
            || (!empty($companyChanges['account_digit']) || array_key_exists('account_digit', $companyChanges))
        ) {
            $company->update(
                [
                    'bank_document_status' => $company->present()->getStatus('pending'),
                ]
            );

            return true;
        }

        return false;
    }

    public function getChangesUpdateCNPJ($company, $documentType)
    {
        $companyChanges = $company->getChanges();

        if (!empty($documentType) && $documentType != $company->document) {
            $company->contract_document_status = $company->presenter()->getStatus('pending');
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
            curl_setopt($ch, CURLOPT_URL, 'https://www.receitaws.com.br/v1/cnpj/'.$cnpj);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            return json_decode($response, true);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $cnpj
     * @return false|mixed
     */
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
        $companyModel = new Company();
        $companies = $companyModel->where('user_id', auth()->user()->account_owner_id)->where('active_flag', true)->get();
        $companyPresenter = $companyModel->present();

        foreach ($companies as $company) {
            if ($company->company_type == $companyPresenter->getCompanyType('juridical person')) {
                if ($company->bank_document_status == $companyPresenter->getBankDocumentStatus('refused') ||
                    $company->address_document_status == $companyPresenter->getAddressDocumentStatus('refused') ||
                    $company->contract_document_status == $companyPresenter->getContractDocumentStatus('refused')) {
                    return $company;
                }
            } else {
                if ($company->bank_document_status == $companyPresenter->getBankDocumentStatus('refused')) {
                    return $company;
                }
            }
        }

        return null;
    }

    public function companyDocumentApproved()
    {
        $companyModel = new Company();
        $companies = $companyModel->where('user_id', auth()->user()->account_owner_id)->where('active_flag', true)->get();
        $companyPresenter = $companyModel->present();

        foreach ($companies as $company) {
            if ($company->company_type == $companyPresenter->getCompanyType('juridical person')) {
                if ($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved') ||
                    $company->address_document_status == $companyPresenter->getAddressDocumentStatus('approved') ||
                    $company->contract_document_status == $companyPresenter->getContractDocumentStatus('approved')) {
                    return $company;
                }
            } else {
                if ($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved')) {
                    return $company;
                }
            }
        }

        return null;
    }

    public function getPendingBalance(Company $company, ?int $liquidationType = null)
    {
        $transactionModel = new Transaction();

        $pendingBalance = $transactionModel->where('company_id', $company->id)
            ->where('status_enum', $transactionModel->present()->getStatusEnum('paid'));

        if(!empty($liquidationType)) {
            if($liquidationType == self::STATEMENT_AUTOMATIC_LIQUIDATION_TYPE) {
                $pendingBalance = $pendingBalance->whereIn('gateway_id', [14, 15])->where('is_waiting_withdrawal', 0);
            }
            elseif($liquidationType == self::STATEMENT_MANUAL_LIQUIDATION_TYPE) {
                $pendingBalance = $pendingBalance->whereNotIn('gateway_id', [14, 15]);
            }
        }

        return $pendingBalance->sum('value');
    }

    public function getAvailableBalance(Company $company, ?int $liquidationType = null): int
    {
        if($liquidationType == self::STATEMENT_MANUAL_LIQUIDATION_TYPE) {
            return $company->balance;
        } elseif($liquidationType == self::STATEMENT_AUTOMATIC_LIQUIDATION_TYPE) {
            return $company->transactions()
                           ->whereIn('gateway_id', [14, 15])
                           ->where('is_waiting_withdrawal', 1)
                           ->whereNull('withdrawal_id')
                           ->sum('value');
        } elseif(empty($liquidationType)) {

            $transactionsValue = $company->transactions()
                                        ->whereIn('gateway_id', [14, 15])
                                        ->where('is_waiting_withdrawal', 1)
                                        ->whereNull('withdrawal_id')
                                        ->sum('value');

            return $transactionsValue + $company->balance;
        }

        throw new LogicException("LiquidationType ( {$liquidationType} ) inválido");

    }

    public function hasCompanyValid()
    {
        $companyModel = new Company();
        $companyPresenter = $companyModel->present();

        $existJuridicalCompany = $companyModel->where([
                                                     ['user_id', auth()->user()->account_owner_id],
                                                     ['company_type', $companyPresenter->getCompanyType('juridical person')],
                                                     ['active_flag', true],
                                                     ['capture_transaction_enabled', true],
                                                     ['bank_document_status', $companyPresenter->getBankDocumentStatus('approved')],
                                                     ['address_document_status', $companyPresenter->getAddressDocumentStatus('approved')],
                                                     ['contract_document_status', $companyPresenter->getContractDocumentStatus('approved')],
                                                 ])->exists();

        $existPhysicalCompany = $companyModel->where([
                                                    ['user_id', auth()->user()->account_owner_id],
                                                    ['company_type', $companyPresenter->getCompanyType('physical person')],
                                                    ['active_flag', true],
                                                    ['capture_transaction_enabled', true],
                                                    ['bank_document_status', $companyPresenter->getBankDocumentStatus('approved')],
                                                ])->exists();

        $valid = $existJuridicalCompany || $existPhysicalCompany;

        return $valid;
    }
    public function verifyFieldsEmptyBraspag(Company $company)
    {
        if ($company->company_type == $company->present()->getCompanyType('juridical person')) {
            // informações basicas
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

    public function verifyFieldsEmpty(Company $company): bool
    {
        if ($company->company_type == $company->present()->getCompanyType('juridical person')) {
            // informações basicas
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
            if (empty($company->user->cellphone) || empty($company->user->email)) {
                $company->update([
                    'get_net_status' => $company->present()->getStatusGetnet('pending'),
                ]);
                return;
            }

            $result = (new GetnetBackOfficeService())->createPjCompany($company);

            $this->updateToReviewStatusGetnet($company, $result);
        } catch (Exception $e) {
            report($e);
        }
    }

    public function createCompanyPfGetnet(Company $company)
    {
        try {
            if ((new UserService())->verifyFieldsEmpty($company->user)) {
                $company->update([
                    'get_net_status' => $company->present()->getStatusGetnet('pending'),
                ]);
                return;
            }

            $result = (new GetnetBackOfficeService())->createPfCompany($company);

            $this->updateToReviewStatusGetnet($company, $result);
        } catch (Exception $e) {
            report($e);
        }
    }

    private function updateToReviewStatusGetnet($company, $result)
    {
        if (empty($result) || empty(json_decode($result)->subseller_id)) {
            $company->update([
                'get_net_status' => $company->present()->getStatusGetnet('error'),
            ]);

            return;
        }

        $company->update(
            [
                'subseller_getnet_id' => json_decode($result)->subseller_id,
                'get_net_status' => $company->present()->getStatusGetnet('review'),
            ]
        );
    }

    public function updateCompanyGetnet(Company $company)
    {
        $getnetService = new GetnetBackOfficeService();
        $userService = new UserService();
        $user = $company->user;

        if ($company->present()->getCompanyType($company->company_type) == 'physical person'
            && (!$userService->verifyFieldsEmpty($user))
        ) {
            $getnetService->updatePfCompany($company);
        } elseif (!empty($user->cellphone) && !empty($user->email)) {
            $getnetService->updatePjCompany($company);
        }

        return [
            'message' => 'success',
            'data' => '',
        ];
    }

    public function unfilledFields(Company $company)
    {
        $arrayFields = [];
        if ($company->company_type == $company->present()->getCompanyType('juridical person')) {
            // informações basicas
            if (empty($company->zip_code)) {
                $arrayFields[] = 'zip_code';
            }
            if (empty($company->street)) {
                $arrayFields[] = 'street';
            }
            if (empty($company->neighborhood)) {
                $arrayFields[] = 'neighborhood';
            }
            if (empty($company->state)) {
                $arrayFields[] = 'state';
            }
            if (empty($company->city)) {
                $arrayFields[] = 'city';
            }
            if (empty($company->country)) {
                $arrayFields[] = 'country';
            }
            // informações complementares
            if (empty($company->extra_document)) {
                $arrayFields[] = 'extra_document';
            }
            if (empty($company->document_issue_date)) {
                $arrayFields[] = 'document_issue_date';
            }
            if (empty($company->document_issuer)) {
                $arrayFields[] = 'document_issuer';
            }
            if (empty($company->document_issuer_state)) {
                $arrayFields[] = 'document_issuer_state';
            }
        } else {
            if (empty($company->fantasy_name)) {
                $arrayFields[] = 'fantasy_name';
            }
            if (empty($company->document)) {
                $arrayFields[] = 'document';
            }
            if (empty($company->bank)) {
                $arrayFields[] = 'bank';
            }
            if (empty($company->agency)) {
                $arrayFields[] = 'agency';
            }
            if (empty($company->account)) {
                $arrayFields[] = 'account';
            }
        }

        return $arrayFields;
    }

    public function getBlockedBalance(Company $company)
    {
        $salesModel = new Sale();
        $transactiosModel = new Transaction();

        return $transactiosModel->whereNull('invitation_id')
            ->where('company_id', $company->id)
            ->where('status_enum', $transactiosModel->present()->getStatusEnum('transfered'))
            ->whereDate('created_at', '>=', '2020-01-01')
            ->whereHas('sale', function ($query) use ($salesModel) {
                $query->where('sales.status', $salesModel->present()->getStatus('in_dispute'))
                    ->orWhere('sales.has_valid_tracking', 0);
            })->select(DB::raw('sum(if(invitation_id is null, value, 0)) as from_sales'),
                DB::raw('sum(if(invitation_id is not null, value, 0)) as from_invites'),
            )->first();
    }

    public function getBlockedBalancePending(Company $company)
    {
        $salesModel = new Sale();
        $transactiosModel = new Transaction();

        return $transactiosModel->whereNull('invitation_id')
            ->where('company_id', $company->id)
            ->where('status_enum', $transactiosModel->present()->getStatusEnum('paid'))
            ->whereDate('created_at', '>=', '2020-01-01')
            ->whereHas('sale', function ($query) use ($salesModel) {
                $query->where('sales.status', $salesModel->present()->getStatus('in_dispute'));
            })->sum('value');
    }

    public function updateCaptureTransactionEnabled(Company $company): void
    {
        try {
            if ($company->get_net_status == (new Company())->present()->getStatusGetnet('approved')) {
                $company->update([
                    'capture_transaction_enabled' => true
                ]);
                if ($this->isDocumentValidated($company->id) && (new UserService())->isDocumentValidated($company->user->id)) {
                    event(new UpdateCompanyGetnetEvent($company));
                }
            } else {
                $company->update([
                    'capture_transaction_enabled' => false
                ]);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
