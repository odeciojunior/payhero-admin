<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Modules\Core\Entities\Affiliate;
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

        $companyDocumentStatus = $companyDocumentValidated ? "approved" : "pending";

        $userAddressDocumentStatus = (new User())->present()->getAddressDocumentStatus(
            $this->user->address_document_status
        );
        $userPersonalDocumentStatus = (new User())->present()->getAddressDocumentStatus(
            $this->user->personal_document_status
        );

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

        $activeFlag = false;
        if (
            $this->active_flag &&
            $this->user->account_is_approved
        ) {
            $activeFlag = true;
        }

        // SEARCH AFFILIATED PROJECTS
        $affiliates = Affiliate::select('affiliates.project_id as id', 'projects.name', 'users_projects.order_priority as order_p', 'projects.status')
            ->join('projects','projects.id','=','affiliates.project_id')
            ->join('users_projects', 'users_projects.project_id', '=', 'projects.id')
            ->where('affiliates.company_id',$this->id);

        if(auth()->user()->deleted_project_filter)
            $affiliates = $affiliates->whereIn('projects.status', [1,2]);
        else
            $affiliates = $affiliates->where('projects.status',1);

        $affiliates = $affiliates->orderBy('projects.status')
            ->orderBy('order_p')
            ->orderBy('projects.id', 'DESC');

        // SEARCH OWN PROJECTS
        $projects = CheckoutConfig::select('checkout_configs.project_id as id','projects.name', 'users_projects.order_priority as order_p', 'projects.status')
            ->join('projects','projects.id','=','checkout_configs.project_id')
            ->join('users_projects', 'users_projects.project_id', '=', 'projects.id')
            ->where('checkout_configs.company_id',$this->id);

        if(auth()->user()->deleted_project_filter)
            $projects = $projects->whereIn('projects.status', [1,2]);
        else
            $projects = $projects->where('projects.status',1);

        $projects = $projects->orderBy('projects.status')
            ->orderBy('order_p')
            ->orderBy('projects.id', 'DESC')
            ->union($affiliates)
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
            'active_flag' => $activeFlag,
            'has_pix_key' => !empty($bankAccount) && $bankAccount->transfer_type=='PIX',
            'company_type' => $this->present()->getCompanyType($this->company_type),
            'user_address_document_status' => $userAddressDocumentStatus,
            'user_personal_document_status' => $userPersonalDocumentStatus,
            'company_is_approved' => $companyIsApproved,
            'order_priority' => $this->order_priority,
            'projects' => $projects
        ];
    }
}
