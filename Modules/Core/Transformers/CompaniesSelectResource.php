<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Modules\Core\Entities\CompanyBankAccount;
use Modules\Core\Entities\CheckoutConfig;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Services\CompanyService;
use stdClass;
use Vinkla\Hashids\Facades\Hashids;

class CompaniesSelectResource extends JsonResource
{
    public function toArray($request): array
    {
        $companyService = new CompanyService();
        $companyDocumentValidated = $companyService->isDocumentValidated($this->id);

        $companyDocumentStatus = ($companyDocumentValidated) ? 'approved' : 'pending';

        $user = $this->user;
        $userAddressDocumentStatus = $user->present()->getAddressDocumentStatus(
            $user->address_document_status
        );
        $userPersonalDocumentStatus = $user->present()->getAddressDocumentStatus(
            $user->personal_document_status
        );

        $bankAccount = CompanyBankAccount::where('company_id',$this->id)->where('is_default',true)->where('status','VERIFIED')->first();

        $companyIsApproved = false;
        if($companyDocumentStatus == "approved" && $userAddressDocumentStatus == "approved" && $userPersonalDocumentStatus == "approved" ) {
            $companyIsApproved = true;
        }

        $projects = CheckoutConfig::select('checkout_configs.project_id as id','projects.name', 'users_projects.order_priority as order_p', 'projects.status')
            ->join('projects','projects.id','=','checkout_configs.project_id')
            ->join('users_projects', 'users_projects.project_id', '=', 'projects.id')
            ->where('checkout_configs.company_id',$this->id)
            ->orderBy('projects.status')
            ->orderBy('order_p')
            ->orderBy('projects.id', 'DESC')
            ->get()
            ->map(function ($project) {
                return (object)[
                    'id' => hashids_encode($project->id),
                    'name'=>$project->name,
                    'order_p'=>$project->order_p,
                    'status'=>$project->status,
                ];
            });

        return [
            'id' => Hashids::encode($this->id),
            'country' => $this->country,
            'name' => $this->company_type == 1 ? 'Pessoa física' : $this->fantasy_name,
            'document' => foxutils()->getDocument($this->document),
            'company_document_status' => $companyDocumentStatus,
            'company_has_sale_before_getnet' => auth()->user()->has_sale_before_getnet,
            'active_flag' => $this->active_flag,
            'has_pix_key' => !empty($bankAccount) && $bankAccount->transfer_type=='PIX',
            'company_type' => $this->present()->getCompanyType($this->company_type),
            'user_address_document_status' => $userAddressDocumentStatus,
            'user_personal_document_status' => $userPersonalDocumentStatus,
            'company_is_approved' => $companyIsApproved,
            'projects' => $projects
        ];
    }
}
