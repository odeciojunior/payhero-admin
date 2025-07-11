<?php

namespace Modules\Users\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Symfony\Component\HttpFoundation\Response;

class UsersApiController extends Controller
{
    public function checkAccount($id)
    {
        try {
            $companyModel = new Company();
            $userModel = new User();

            $user = $userModel->find($id);

            return response()->json(
                [
                    "data" => [
                        "account" => $userModel->present()->getAccountStatus($user->account_is_approved),
                        "user" => [
                            "personal_document" => $userModel
                                ->present()
                                ->getPersonalDocumentStatus($user->personal_document_status),
                            "address_document" => $userModel
                                ->present()
                                ->getAddressDocumentStatus($user->address_document_status),
                        ],
                        "company" => [
                            "type" =>
                                $user->companies->count() > 0
                                    ? $companyModel->present()->getCompanyType($user->companies->first()->company_type)
                                    : null,
                            "address_document" =>
                                $user->companies->count() > 0
                                    ? $companyModel
                                        ->present()
                                        ->getAddressDocumentStatus($user->companies->first()->address_document_status)
                                    : null,
                            "contract_document" =>
                                $user->companies->count() > 0
                                    ? $companyModel
                                        ->present()
                                        ->getContractDocumentStatus($user->companies->first()->contract_document_status)
                                    : null,
                        ],
                    ],
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    "message" => $e->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
