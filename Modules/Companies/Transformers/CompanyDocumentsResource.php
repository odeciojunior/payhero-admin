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
        $companyDocumentModel    = app(CompanyDocument::class);

        $temporaryUrl = '';
        if (!empty($this->document_url)) {
            $temporaryUrl = FoxUtils::getAwsSignedUrl($this->document_url,10,'documents');
        }

        return [
            'date'           => $this->created_at ? Carbon::parse($this->created_at)->format('d/m/Y H:i:s') : '',
            'status'         => $companyDocumentModel->present()->getTypeEnum($this->status),
            'document_url'   => $temporaryUrl,
            'refused_reason' => $this->refused_reason,
        ];
    }
}


