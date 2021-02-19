<?php

namespace Modules\Profile\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Services\AmazonFileService;

/**
 * Class ProfileDocumentsResource
 * @package Modules\Profile\Transformers
 */
class ProfileDocumentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {

        $userDocumentModel = app(UserDocument::class);
        $temporaryUrl = '';

        if (!empty($this->document_url) && strstr($this->document_url, 'amazonaws')) {
            $amazonFileService = app(AmazonFileService::class);
            $amazonFileService->setDisk('s3_documents');
            $temporaryUrl = $amazonFileService->getTemporaryUrlFile($this->document_url, 180);
        }

        return [
            'date' => $this->created_at ? Carbon::parse($this->created_at)->format('d/m/Y H:i:s') : '',
            'status' => $userDocumentModel->present()->getTypeEnum($this->status),
            'document_url' => $temporaryUrl,
            'refused_reason' => $this->refused_reason,
        ];
    }
}
