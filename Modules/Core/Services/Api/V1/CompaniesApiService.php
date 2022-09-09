<?php

namespace Modules\Core\Services\Api\V1;

use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\User;
use Vinkla\Hashids\Facades\Hashids;

class CompaniesApiService
{
    public static function prepareRequestData(array $request): array
    {
        return [];
    }

    public static function uploadDocuments(string $id, array $data): array
    {
        $user = User::find(current(Hashids::decode($id)));

        $sDrive = Storage::disk('s3_documents');

        $document = preg_replace('/[^0-9]/', '', $user['document']);
        $files = $data['file_to_uploads'];
        foreach($files as $file) {
            $documentRename = $file['type'].'.'.$file['file']->extension();

            $allFiles = $sDrive->allFiles("uploads/register/user/$document/private/documents");

            $fileServer = collect($allFiles)->first(function ($value) use ($data) {
                return strpos($value, $data['type']);
            });

            if ($sDrive->exists($fileServer)) {
                $sDrive->delete($fileServer);
            }

            $sDrive->putFileAs(
                "uploads/register/user/$document/private/documents",
                $document,
                $documentRename,
                'private'
            );

            $urlPath = $sDrive->temporaryUrl(
                'uploads/register/user/'.$document.'/private/documents/'.$documentRename,
                now()->addHours(24)
            );
        }

        return [
            'message' => 'Arquivo(s) enviado(s) com sucesso.'
        ];
    }
}
