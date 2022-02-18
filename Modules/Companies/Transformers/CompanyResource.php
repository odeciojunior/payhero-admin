<?php

namespace Modules\Companies\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\CurrencyQuotationService;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class CompanyResource
 * @package Modules\Companies\Transformers
 */
class CompanyResource extends JsonResource
{
    /**
     * The resource instance.
     * @var Company
     */
    public $resource;

    /**
     * @param Request $request
     * @return array
     * @throws PresenterException
     */
    public function toArray($request)
    {
        $presenter = $this->resource->present();

        $documentStatus = $presenter->allStatusPending() ? $presenter->getStatus(3) : $presenter->getStatus(1);
        $companyService = new CompanyService();
        $refusedDocuments = $companyService->getRefusedDocuments($this->resource->id);
        $user = $this->resource->user;
        $project = UserProject::whereHas('project', function ($query) {
            $query->where('status', 1);
        })
        ->where('company_id', $this->resource->id)
        ->first();

        $currencyQuotationService = new CurrencyQuotationService();
        $lastUsdQuotation = $currencyQuotationService->getLastUsdQuotation();

        $companyDocumentValidated = $companyService->isDocumentValidated($this->resource->id);

        $companyDocumentStatus = ($companyDocumentValidated) ? 'approved' : 'pending';

        $userAddressDocumentStatus = $presenter->getAddressDocumentStatus($user->address_document_status);
        $userPersonalDocumentStatus = $presenter->getAddressDocumentStatus($user->personal_document_status);

        $companyIsApproved = false;
        if($companyDocumentStatus == "approved" && $userAddressDocumentStatus == "approved" && $userPersonalDocumentStatus == "approved" ) {
            $companyIsApproved = true;
        }

        return [
            'id_code' => Hashids::encode($this->resource->id),
            'user_code' => Hashids::encode($this->resource->user_id),
            'support_email' => $this->resource->support_email ?? '',
            'support_telephone' => $this->resource->support_telephone ?? '',
            'fantasy_name' => $this->resource->company_type == 1 ? 'Pessoa física' : $this->resource->fantasy_name ?? '',
            'document' => strlen($this->resource->document) == 14 ? FoxUtils::mask(
                $this->resource->document,
                '##.###.###/####-##'
            ) : (strlen($this->resource->document) == 11 ? FoxUtils::mask(
                $this->resource->document,
                '###.###.###-##'
            ) : $this->resource->document),
            'zip_code' => $this->resource->zip_code ?? '',
            'country' => $this->resource->country ?? '',
            'country_translated' => $this->resource->country ? __('definitions.enum.country.' . $this->resource->country) : '',
            'state' => $this->resource->state ?? '',
            'city' => $this->resource->city ?? '',
            'street' => $this->resource->street ?? '',
            'complement' => $this->resource->complement ?? '',
            'neighborhood' => $this->resource->neighborhood ?? '',
            'number' => $this->resource->number ?? '',
            'bank' => $this->resource->bank ?? '',
            'agency' => $this->resource->agency ?? '',
            'agency_digit' => $this->resource->agency_digit ?? '',
            'account' => $this->resource->account ?? '',
            'account_digit' => $this->resource->account_digit ?? '',
            'document_status' => $documentStatus,
            'bank_document_status' => $presenter->getBankDocumentStatus($this->resource->bank_document_status),
            'address_document_status' => $presenter->getAddressDocumentStatus($this->resource->address_document_status),
            'contract_document_status' => $presenter->getContractDocumentStatus($this->resource->contract_document_status),
            'bank_document_translate' => __('definitions.enum.status.' . $presenter->getBankDocumentStatus()),
            'address_document_translate' => __('definitions.enum.status.' . $presenter->getAddressDocumentStatus()),
            'contract_document_translate' => __('definitions.enum.status.' . $presenter->getContractDocumentStatus()),
            'refusedDocuments' => $refusedDocuments,
            'type' => $this->company_type,
            'type_company' => $presenter->getCompanyType($this->company_type),
            'user_address_document_status' => $presenter->getAddressDocumentStatus($user->address_document_status),
            'user_personal_document_status' => $presenter->getAddressDocumentStatus($user->personal_document_status),
            'account_type' => $this->account_type ?? '',
            'document_issue_date' => !empty($this->document_issue_date) ? Carbon::parse($this->document_issue_date)->format('Y-m-d') : '',
            'document_issuer' => $this->document_issuer ?? '',
            'document_issuer_state' => $this->document_issuer_state ?? '',
            'extra_document' => $this->extra_document ?? '',
            'active_flag' => $this->active_flag,
            'has_project' => !empty($project),
            'transaction_rate' => $this->transaction_rate,
            'gateway_tax' => $this->gateway_tax,
            'installment_tax' => $this->installment_tax,
            'gateway_release_money_days' => $this->gateway_release_money_days,
            'currency_quotation' => $lastUsdQuotation->value,
            'company_is_approved' => $companyIsApproved
        ];
    }
}
