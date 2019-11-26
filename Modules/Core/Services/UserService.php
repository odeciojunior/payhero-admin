<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Companies\Transformers\CompanyResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;

/**
 * Class CompaniesService
 * @package Modules\Core\Services
 */
class UserService
{
    public function isDocumentValidated()
    {
        $userModel     = new User();
        $user          = auth()->user();
        $userPresenter = $userModel->present();
        if (!empty($user)) {
            if ($user->address_document_status == $userPresenter->getAddressDocumentStatus('approved') &&
                $user->personal_document_status == $userPresenter->getPersonalDocumentStatus('approved')) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    public function getRefusedDocuments()
    {
        $userModel        = new User();
        $userPresenter    = $userModel->present();
        $user             = auth()->user();
        $refusedDocuments = collect();
        if (!empty($user)) {
            foreach ($user->userDocuments as $document) {
                if (!empty($document->refused_reason)) {
                    $dataDocument = [
                        'date'            => $document->created_at->format('d/m/Y'),
                        'type_translated' => __('definitions.enum.user_document_type.' . $userPresenter->getDocumentType($document->document_type_enum)),
                        'document_url'    => $document->document_url,
                        'refused_reason'  => $document->refused_reason,
                    ];
                    $refusedDocuments->push(collect($dataDocument));
                }
            }
        }

        return $refusedDocuments;
    }

    public function verifyCpf($cpf)
    {
        $userModel     = new User();
        $cpf           = preg_replace("/[^0-9]/", "", $cpf);
        $userPresenter = $userModel->present();

        $user = $userModel->where(
            [['document', 'like', '%' . $cpf . '%'], ['address_document_status', $userPresenter->getAddressDocumentStatus('approved')], ['personal_document_status', $userPresenter->getPersonalDocumentStatus('approved')]]
        )->first();
        if (!empty($user)) {
            return true;
        }

        return false;
    }
}
