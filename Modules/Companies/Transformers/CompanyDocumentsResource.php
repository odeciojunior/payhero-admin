<?php

namespace Modules\Companies\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Services\AmazonFileService;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Core\Services\FoxUtils;

class CompanyDocumentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $digitalOceanFileService = app(DigitalOceanFileService::class);
        $amazonFileService = app(AmazonFileService::class);
        $companyDocumentModel    = app(CompanyDocument::class);

        $temporaryUrl = '';
        if (!empty($this->document_url)) {
            // Gera o Link temporário de acordo com o driver

            if (strstr($this->document_url, 'digitaloceanspaces')) {
                $temporaryUrl = $digitalOceanFileService->getTemporaryUrlFile($this->document_url, 180);
            }

            if (strstr($this->document_url, 'amazonaws')) {
                $amazonFileService->setDisk('s3_documents');
                $temporaryUrl = $amazonFileService->getTemporaryUrlFile($this->document_url, 180);
            }
        }

        return [
            'date'           => $this->created_at ? Carbon::parse($this->created_at)->format('d/m/Y H:i:s') : '',
            'status'         => $companyDocumentModel->present()->getTypeEnum($this->status),
            'document_url'   => $temporaryUrl,
            'refused_reason' => $this->refused_reason,
        ];
    }
}
