<?php

namespace Modules\Plans\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\UserService;
use Vinkla\Hashids\Facades\Hashids;

class PlansResource extends JsonResource
{
    public function toArray($request)
    {
        $companyService           = new CompanyService();
        $userService              = new UserService();
        $companyId                = $this->project->usersProjects[0]->company->id;
        $companyDocumentValidated = $companyService->isDocumentValidated($companyId);
        $userDocumentValidated    = $userService->isDocumentValidated();

        return [
            'id'                => Hashids::encode($this->id),
            'name'              => $this->name,
            'description'       => $this->description == null ? '' : $this->description,
            'code'              => isset($this->project->domains[0]->name) ? 'https://checkout.' . $this->project->domains[0]->name . '/' . $this->code : 'Domínio não configurado',
            'price'             => 'R$ ' . number_format(intval(preg_replace("/[^0-9]/", "", $this->price)) / 100, 2, ',', '.'),
            'status'            => isset($this->project->domains[0]->name) ? 1 : 0,
            'status_code'       => $this->status,
            'status_translated' => isset($this->project->domains[0]->name) ? 'Ativo' : 'Desativado',
            'document_status'   => ($companyDocumentValidated && $userDocumentValidated) ? 'approved' : 'pending',
        ];
    }
}
