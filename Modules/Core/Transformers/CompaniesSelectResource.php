<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\CompanyBankAccount;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Services\CompanyService;
use Vinkla\Hashids\Facades\Hashids;

class CompaniesSelectResource extends JsonResource
{
    public function toArray($request): array
    {
        $companyService = new CompanyService();
        $companyDocumentValidated = $companyService->isDocumentValidated($this->id);

        $companyDocumentStatus = $companyDocumentValidated ? "approved" : "pending";

        $userAddressDocumentStatus = (new User())
            ->present()
            ->getAddressDocumentStatus($this->user->address_document_status);
        $userPersonalDocumentStatus = (new User())
            ->present()
            ->getAddressDocumentStatus($this->user->personal_document_status);

        $bankAccount = CompanyBankAccount::where("company_id", $this->id)
            ->where("is_default", true)
            ->where("status", "VERIFIED")
            ->first();

        $companyIsApproved = false;
        if (
            $companyDocumentStatus == "approved" &&
            $userAddressDocumentStatus == "approved" &&
            $userPersonalDocumentStatus == "approved"
        ) {
            $companyIsApproved = true;
        }

        return [
            "id" => Hashids::encode($this->id),
            "country" => $this->country,
            "name" => $this->company_type == 1 ? "Pessoa física" : $this->fantasy_name,
            "document" => foxutils()->getDocument($this->document),
            "company_document_status" => $companyDocumentStatus,
            "company_has_sale_before_getnet" => auth()->user()->has_sale_before_getnet,
            "active_flag" => $this->active_flag,
            "has_pix_key" => !empty($bankAccount) && $bankAccount->transfer_type == "PIX",
            "company_type" => $this->present()->getCompanyType($this->company_type),
            "user_address_document_status" => $userAddressDocumentStatus,
            "user_personal_document_status" => $userPersonalDocumentStatus,
            "company_is_approved" => $companyIsApproved,
        ];
    }
}
