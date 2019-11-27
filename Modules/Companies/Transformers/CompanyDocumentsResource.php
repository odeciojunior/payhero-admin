<?php

namespace Modules\Companies\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Services\DigitalOceanFileService;

class CompanyDocumentsResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $digitalOceanFileService = app(DigitalOceanFileService::class);
        $companyDocumentModel    = app(CompanyDocument::class);

        $temporaryUrl = '';
        if (!empty($this->document_url)) {
            $temporaryUrl = $digitalOceanFileService->getTemporaryUrlFile($this->document_url, 100);
        }

        return [
            'date'           => $this->created_at ? Carbon::parse($this->created_at)->format('d/m/Y H:i:s') : '',
            'status'         => $companyDocumentModel->present()->getTypeEnum($this->status),
            'document_url'   => $temporaryUrl,
            'refused_reason' => $this->refused_reason,
        ];
    }
}
