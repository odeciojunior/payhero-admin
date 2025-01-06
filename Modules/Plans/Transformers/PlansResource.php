<?php

namespace Modules\Plans\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Modules\Core\Entities\Company;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\UserService;
use Vinkla\Hashids\Facades\Hashids;

class PlansResource extends JsonResource
{
    public function toArray($request)
    {
        $companyService = new CompanyService();
        $userService = new UserService();
        $companyId = $this->project->usersProjects[0]->company->id;
        $companyDocumentValidated = $companyService->isDocumentValidated($companyId);
        $userDocumentValidated = $userService->isDocumentValidated();

        if (FoxUtils::isProduction()) {
            if ($this->project->nuvemshop_id) {
                $link = env("CHECKOUT_URL", "https://checkout.azcend.com.br") . "/" . $this->code;
            } else {
                $link = isset($this->project->domains[0]->name)
                    ? "https://checkout." . $this->project->domains[0]->name . "/" . $this->code
                    : "Domínio não configurado";
            }
        } else {
            $link = env("CHECKOUT_URL", "http://dev.checkout.com.br") . "/" . $this->code;
        }

        $user = auth()->user();
        if ($user->company_default == Company::DEMO_ID) {
            $link = "https://demo.azcend.com.br/" . $this->code;
            if (env("APP_ENV") == "local") {
                $link = env("CHECKOUT_URL", "http://dev.checkout.com.br") . "/" . $this->code;
            }
        }

        $costCurrency = !is_null($this->project->notazz_configs) ? json_decode($this->project->notazz_configs) : null;

        $limit_name = 24;
        $limit_description = 38;

        $status = (isset($this->project->nuvemshop_id)
                ? ($this->project->status
                    ? 1
                    : 0)
                : isset($this->project->domains[0]->name))
            ? 1
            : 0;

        return [
            "id" => Hashids::encode($this->id),
            "name" => $this->name,
            "name_short" => Str::limit($this->name, $limit_name),
            "name_short_flag" => mb_strwidth($this->name, "UTF-8") <= $limit_name ? false : true,
            "description" => $this->description,
            "description_short" => Str::limit($this->description, $limit_description),
            "description_short_flag" => mb_strwidth($this->description, "UTF-8") <= $limit_description ? false : true,
            "code" => $link,
            "price" => 'R$ ' . number_format(intval(preg_replace("/[^0-9]/", "", $this->price)) / 100, 2, ",", "."),
            "status" => $status,
            "status_code" => $this->status,
            "status_translated" => $status ? "Ativo" : "Desativado",
            "document_status" => $companyDocumentValidated && $userDocumentValidated ? "approved" : "pending",
            "currency_project" => $costCurrency->cost_currency_type ?? 1,
            "products_length" => count($this->productsPlans),
        ];
    }
}
