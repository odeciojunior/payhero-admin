<?php

namespace Modules\Core\Services\Api\V1;

use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\User;
use Vinkla\Hashids\Facades\Hashids;

class SubsellersApiService
{
    private const BALANCE = '0';
    private const VERIFIED_EMAIL = 1;
    private const VERIFIED_CELLPHONE = 1;
    private const DEFAULT_LEVEL = 1;

    public static function prepareRequestData(array $request): array
    {
        $requestData = $request;

        $requestData['account_owner_id'] = null;
        $requestData['subseller_owner_id'] = request()->user_id;
        $requestData['balance'] = self::BALANCE;
        $requestData['email_verified'] = self::VERIFIED_EMAIL;
        $requestData['document'] = $requestData['document'];
        $requestData['cellphone'] = $requestData['cellphone'];
        $requestData['cellphone_verified'] = self::VERIFIED_CELLPHONE;
        $requestData['release_count'] = 0;
        $requestData['password'] = bcrypt($requestData['password']);
        $requestData['level'] = self::DEFAULT_LEVEL;

        $penaltyValues = [
            'contestation_penalty_level_1' => '2000',
            'contestation_penalty_level_2' => '3000',
            'contestation_penalty_level_3' => '5000',
        ];

        $requestData['contestation_penalties_taxes'] = json_encode($penaltyValues);

        return $requestData;
    }

    public static function uploadDocuments(string $id, array $data): array
    {
        $user = User::find(current(Hashids::decode($id)));

        $sDrive = Storage::disk('s3_documents');

        $document = preg_replace('/[^0-9]/', '', $user['document']);
        $files = $data['file_to_uploads'];
        foreach($files as $file) {
            dd($file);

            $documentRename = $file['type'].'.'.$file['file']->extension();

            dd($documentRename);

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
            'message' => 'Arquivo enviado com sucesso.',
            'path' => $urlPath,
            'fileName' => $document->getClientOriginalName(),
            'fileType' => $document->extension(),
        ];
    }
}
