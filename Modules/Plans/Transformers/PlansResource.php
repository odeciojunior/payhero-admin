<?php

namespace Modules\Plans\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\UserService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Str;

class PlansResource extends JsonResource
{
    public function toArray($request)
    {
        $companyService           = new CompanyService();
        $userService              = new UserService();
        $companyId                = $this->project->usersProjects[0]->company->id;
        $companyDocumentValidated = $companyService->isDocumentValidated($companyId);
        $userDocumentValidated    = $userService->isDocumentValidated();

        if(FoxUtils::isProduction()) {
            $link = isset($this->project->domains[0]->name) ? 'https://checkout.' . $this->project->domains[0]->name . '/' . $this->code : 'Domínio não configurado';
        } else {
            $link = env('CHECKOUT_URL', 'http://dev.checkout.com.br') . '/' . $this->code;
        }

        $costCurrency = (!is_null($this->project->notazz_configs)) ? json_decode($this->project->notazz_configs) : null;

        $limit_name = 24;

        return [
            'id'                => Hashids::encode($this->id),
            'name'              => $this->name,
            'name_short'        => Str::limit($this->name, $limit_name),
            'name_short_flag'   => mb_strwidth($this->name, 'UTF-8') <= $limit_name ? false : true,
            'description'       => $this->description == null ? '' : $this->description,
            'code'              => $link,
            'price'             => 'R$' . number_format(intval(preg_replace("/[^0-9]/", "", $this->price)) / 100, 2, ',', '.'),
            'status'            => isset($this->project->domains[0]->status) ? 1 : 0,
            'status_code'       => $this->status,
            'status_translated' => isset($this->project->domains[0]->name) ? 'Ativo' : 'Desativado',
            'document_status'   => ($companyDocumentValidated && $userDocumentValidated) ? 'approved' : 'pending',
            'currency_project'  => $costCurrency->cost_currency_type ?? 1,
            'products_length'   => count($this->productsPlans),
        ];
    }
}
