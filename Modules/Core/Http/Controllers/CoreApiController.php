<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Companies\Http\Requests\CompanyCreateRequest;
use Modules\Companies\Http\Requests\CompanyUpdateRequest;
use Modules\Companies\Http\Requests\CompanyUploadDocumentRequest;
use Modules\Core\Transformers\CompaniesSelectResource;
use Modules\Companies\Transformers\CompanyCpfResource;
use Modules\Companies\Transformers\CompanyDocumentsResource;
use Modules\Core\Transformers\CompanyResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Entities\UserInformation;
use Modules\Core\Services\AmazonFileService;
use Modules\Core\Services\BankService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\CompanyServiceGetnet;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\UserService;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class CoreApiController
 * @package Modules\Core\Http\Controllers
 */
class CoreApiController extends Controller
{
    public function verifyAccount($id)
    {
        try {
            $userModel = new User();
            $userService = new UserService();

            $companyModel = new Company();
            $companyService = new CompanyService();

            $user = User::find(current(Hashids::decode($id)));

            $userInformations = UserInformation::where('document', $user->document)->exists();

            $userStatus = null;
            $userRedirect = null;
            if ($userService->haveAnyDocumentPending()) {
                $userStatus = $userModel->present()->getAddressDocumentStatus(UserDocument::STATUS_PENDING);
                $userRedirect = 'personal-info';
            }

            if ($userService->haveAnyDocumentAnalyzing()) {
                $userStatus = $userModel->present()->getAddressDocumentStatus(UserDocument::STATUS_ANALYZING);
            }

            if ($userService->haveAnyDocumentApproved()) {
                $userStatus = $userModel->present()->getAddressDocumentStatus(UserDocument::STATUS_APPROVED);
            }

            if ($userService->haveAnyDocumentRefused()) {
                $userStatus = $userModel->present()->getAddressDocumentStatus(UserDocument::STATUS_REFUSED);
                $userRedirect = '/personal-info';
            }

            $companyStatus = null;
            $companyRedirect = null;
            if ($user->companies->count() > 0) {
                $companyStatus = null;
                $companyRedirect = '/companies';
            } else {
                $companyApproved = $companyService->companyDocumentApproved();
                if (!empty($companyApproved)) {
                    $companyStatus = $companyModel->present()->getAddressDocumentStatus(CompanyDocument::STATUS_APPROVED);
                    $companyRedirect = '/companies';
                } else {
                    $companyPending = $companyService->companyDocumentPending();
                    if (!empty($companyPending)) {
                        $companyStatus = $companyModel->present()->getAddressDocumentStatus(CompanyDocument::STATUS_PENDING);
                        $companyRedirect = '/companies/company-detail/'. Hashids::encode($companyPending->id);
                    }

                    $companyAnalyzing = $companyService->companyDocumentAnalyzing();
                    if (!empty($companyAnalyzing)) {
                        $companyStatus = $companyModel->present()->getAddressDocumentStatus(CompanyDocument::STATUS_ANALYZING);
                        $companyRedirect = '';
                    }

                    $companyRefused = $companyService->companyDocumentRefused();
                    if (!empty($companyRefused)) {
                        $companyStatus = $companyModel->present()->getAddressDocumentStatus(CompanyDocument::STATUS_REFUSED);
                        $companyRedirect = '/companies/company-detail/'. Hashids::encode($companyRefused->id);
                    }
                }
            }

            if (!$user->account_is_approved) {
                if ($userStatus == 'approved' && $userInformations == true && !empty($companyApproved)) {
                    $user->update([
                        'account_is_approved' => 1
                    ]);
                }
            }

            return response()->json([
                'data' => [
                    'account' => $userModel->present()->getAccountStatus($user->account_is_approved),
                    'user' => [
                        'status' => $userStatus,
                        'document' => $user->document,
                        'informations' => $userInformations,
                        'link' => $userRedirect,
                    ],
                    'company' => [
                        'status' => $companyStatus,
                        'link' => $companyRedirect,
                    ]
                ]
            ], Response::HTTP_OK);
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function verifyDocuments()
    {
        try {
            $companyService = new CompanyService();
            $userService = new UserService();
            $userModel = new User();

            $userDocumentRefused = $userService->haveAnyDocumentRefused();

            $link = null;
            $refused = false;
            $analyzing = false;
            $user = auth()->user();
            $accountType = ($user->id == $user->account_owner_id) ? 'owner' : 'collaborator';

            if ($userDocumentRefused) {
                $refused = true;
                $link = '/personal-info';
            } else {
                $companyDocumentRefused = $companyService->companyDocumentRefused();
                $companyDocumentApproved = $companyService->companyDocumentApproved();
                if (empty($companyDocumentApproved) && !empty($companyDocumentRefused)) {
                    $refused = true;
                    $companyCode = Hashids::encode($companyDocumentRefused->id);
                    if ($companyDocumentRefused->company_type == $companyDocumentRefused->present()->getCompanyType(
                            'physical person'
                        )
                    ) {
                        $link = "/personal-info";
                    } else {
                        $link = "/companies/company-detail/${companyCode}";
                    }
                } else {
                    $userValid = $userService->isDocumentValidated();
                    if (!$userValid) {
                        $analyzing = true;
                    } else {
                        if (!auth()->user()->account_is_approved) {
                            $analyzing = true;
                        }
                    }
                }
            }

            if (env('ACCOUNT_FRONT_URL') && empty($link)) {
                $link = env('ACCOUNT_FRONT_URL') . $link;
            }

            return response()->json(
                [
                    'message' => 'Documentos verificados!',
                    'analyzing' => $analyzing,
                    'refused' => $refused,
                    'link' => $link,
                    'accountType' => $accountType,
                    'accountStatus' => $userModel->present()->getStatus($user->status),
                ]
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(['error' => 'Erro ao verificar documentos'], 400);
        }
    }


    public function companies(Request $request)
    {
        try {
            $companyService = new CompanyService();

            $paginate = true;
            if ($request->has('select') && $request->input('select')) {
                $paginate = false;
            }

            return $companyService->getCompaniesUser($paginate);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde',
                ],
                400
            );
        }
    }


    public function getCompanies()
    {
        try {
            $companyModel = new Company();
            $companies = $companyModel->newQuery()->where('user_id', auth()->user()->account_owner_id)
                ->orderBy('order_priority')->get();

            return CompaniesSelectResource::collection($companies);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro ao tentar buscar dados, tente novamente mais tarde',
                ],
                400
            );
        }
    }

}
