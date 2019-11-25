<?php

namespace Modules\Profile\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Services\DigitalOceanFileService;

/**
 * Class ProfileDocumentsResource
 * @package Modules\Profile\Transformers
 */
class ProfileDocumentsResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $digitalOceanFileService = app(DigitalOceanFileService::class);
        $userDocumentModel       = app(UserDocument::class);

        $temporaryUrl = '';
        if (!empty($this->document_url)) {
            $temporaryUrl = $digitalOceanFileService->getTemporaryUrlFile($this->document_url, 180);
        }

        return [
            'date'         => $this->created_at ? Carbon::parse($this->created_at)->format('d/m/Y H:i:s') : '',
            'status'       => $userDocumentModel->present()->getTypeEnum($this->status),
            'document_url' => $temporaryUrl,

        ];
    }
}
