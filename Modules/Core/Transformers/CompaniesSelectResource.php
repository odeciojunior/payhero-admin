<?php

declare(strict_types=1);

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\CheckoutConfig;
use Modules\Core\Entities\CompanyBankAccount;
use Modules\Core\Entities\User;
use Modules\Core\Enums\User\UserBiometryStatusEnum;
use Modules\Core\Services\CompanyService;
use Vinkla\Hashids\Facades\Hashids;

final class CompaniesSelectResource extends JsonResource
{
    public function toArray($request): array
    {
        $companyService = new CompanyService();
        $companyDocumentValidated = $companyService->isDocumentValidated($this->id);

        $companyDocumentStatus = $companyDocumentValidated ? "approved" : "pending";

        $userAddressDocumentStatus = (new User())
            ->present()
            ->getAddressDocumentStatus($this->user->address_document_status);

        $bankAccount = CompanyBankAccount::where("company_id", $this->id)
            ->where("is_default", true)
            ->where("status", "VERIFIED")
            ->first();

        $companyIsApproved = false;
        if (
            "approved" === $companyDocumentStatus &&
            "approved" === $userAddressDocumentStatus
        ) {
            $companyIsApproved = true;
        }

        $activeFlag = false;
        if ($this->active_flag && $this->user->account_is_approved) {
            $activeFlag = true;
        }

        // SEARCH AFFILIATED PROJECTS
        $affiliates = Affiliate::select(
            "affiliates.project_id as id",
            "projects.name",
            "users_projects.order_priority as order_p",
            "projects.status",
            DB::Raw("'' as prefix")
        )
            ->join("projects", "projects.id", "=", "affiliates.project_id")
            ->join("users_projects", "users_projects.project_id", "=", "projects.id")
            ->where("affiliates.company_id", $this->id);

        if (auth()->user()->deleted_project_filter) {
            $affiliates = $affiliates->whereIn("projects.status", [1, 2]);
        } else {
            $affiliates = $affiliates->where("projects.status", 1);
        }

        $affiliates = $affiliates
            ->orderBy("projects.status")
            ->orderBy("order_p")
            ->orderBy("projects.id", "DESC");

        // SEARCH OWN PROJECTS
        $projects = CheckoutConfig::select(
            "checkout_configs.project_id as id",
            "projects.name",
            "users_projects.order_priority as order_p",
            "projects.status",
            DB::Raw("'' as prefix")
        )
            ->join("projects", "projects.id", "=", "checkout_configs.project_id")
            ->join("users_projects", "users_projects.project_id", "=", "projects.id")
            ->where("checkout_configs.company_id", $this->id);

        if (auth()->user()->deleted_project_filter) {
            $projects = $projects->whereIn("projects.status", [1, 2]);
        } else {
            $projects = $projects->where("projects.status", 1);
        }

        $tokens = DB::table("api_tokens")
            ->select(
                "id as project_id",
                "description as name",
                DB::Raw("'1' as order_p"),
                DB::Raw("'1' as status"),
                DB::Raw("'TOKEN-' as prefix")
            )
            ->where("user_id", $this->user->id)
            ->whereIn("integration_type_enum", [4, 5])
            ->whereNull("deleted_at")
            ->where("company_id", $this->id);

        $projects = $projects
            ->orderBy("projects.status")
            ->orderBy("order_p")
            ->orderBy("projects.id", "DESC")
            ->union($affiliates)
            ->union($tokens)
            ->get()
            ->map(function ($project) {
                return (object) [
                    "id" => $project->prefix . hashids_encode($project->id),
                    "name" => $project->name,
                    "order_p" => $project->order_p,
                    "status" => $project->status,
                ];
            });

        return [
            "id" => Hashids::encode($this->id),
            "country" => $this->country,
            "name" => 1 === $this->company_type ? "Pessoa física" : $this->fantasy_name,
            "document" => foxutils()->getDocument($this->document),
            "company_document_status" => $companyDocumentStatus,
            "company_has_sale_before_getnet" => auth()->user()->has_sale_before_getnet,
            "active_flag" => $activeFlag,
            "has_pix_key" => ! empty($bankAccount) && "PIX" === $bankAccount->transfer_type,
            "company_type" => $this->present()->getCompanyType($this->company_type),
            "user_address_document_status" => $userAddressDocumentStatus,
            "user_personal_document_status" => UserBiometryStatusEnum::isApproved($this->user->biometry_status),
            "company_is_approved" => $companyIsApproved,
            "order_priority" => $this->order_priority,
            "projects" => $projects,
        ];
    }
}
