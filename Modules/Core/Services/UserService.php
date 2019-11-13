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
}
