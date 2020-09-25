<?php

namespace Modules\Register\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        return view('register::create');
    }

    public function loginAsSomeUser($userId)
    {
        $userIdDecode = Hashids::decode($userId)[0];

        if (!empty($userIdDecode)) {
            auth()->loginUsingId($userIdDecode);

            return response()->redirectTo('/dashboard');
        }

        return view('errors.404');
    }

    public function uploudDocumentsRegistered(array $files)
    {
        $userModel = new User();
        $userDocument = new UserDocument();
        $user = $userModel->find(2349);

        $companyModel = new Company();
        $companyDocumentModel = new CompanyDocument();


        if (env('APP_ENV') == 'local')
            $sDrive = Storage::disk('local');
        else
            $sDrive = Storage::disk('s3');

        $company = $companyModel->where('user_id', auth()->user()->account_owner_id)->first();
        if (Gate::allows('uploadDocuments', [$user]) && Gate::allows('uploadDocuments', [$company])) {
            try {
                foreach ($files as $document) {
                    if ($documentType = $document->fileName ?? '') {
                        return false;
                    }

                    /**
                     * Uploud Usuário
                     */
                    if (in_array($document->fileName, ['personal_document', 'address_document'])) {
                        $amazonPathUser = $sDrive->putFileAs(
                            'uploads/register/user/' . $user->present()->get('document') . '/public/documents',
                            $document,
                            null,
                            'private'
                        );
                        /**
                         * Salva status do documentos no Banco | (Usuário)
                         */
                        $userDocument->create(
                            [
                                'user_id' => auth()->user()->account_owner_id,
                                'document_url' => $amazonPathUser,
                                'document_type_enum' => $documentType,
                                'status' => $userDocument->present()->getTypeEnum('analyzing'),
                            ]
                        );

                        if ($documentType == $user->present()->getDocumentType('personal_document')) {
                            $user->update(
                                [
                                    'personal_document_status' => $user->present()
                                        ->getPersonalDocumentStatus('analyzing'),
                                ]
                            );
                        }

                        if ($documentType == $user->present()->getDocumentType('address_document')) {
                            $user->update(
                                [
                                    'address_document_status' => $user->present()->getAddressDocumentStatus('analyzing'),
                                ]
                            );
                        }
                    }
                    /**
                     * Uploud Empresa
                     */
                    if (in_array($document->fileName, ['bank_document_status', 'address_document_status', 'contract_document_status'])) {
                        $amazonPathCompanies = $sDrive->putFileAs(
                            'uploads/user/' . auth()->user()->account_owner_id
                            . '/companies/' . $company->id . '/public/documents',
                            $document,
                            null,
                            'private'
                        );

                        /**
                         * Salva status do documentos no Banco | (Empresa)
                         */
                        $companyDocumentModel->create(
                            [
                                'company_id' => $company->id,
                                'document_url' => $amazonPathCompanies,
                                'document_type_enum' => $documentType,
                                'status' => $companyDocumentModel->present()->getTypeEnum('analyzing'),
                            ]
                        );

                        if ($documentType == $company->present()->getDocumentType('bank_document_status')) {
                            $company->update(
                                [
                                    'bank_document_status' => $company->present()
                                        ->getBankDocumentStatus('analyzing'),
                                ]
                            );
                        }
                        if ($documentType == $company->present()->getDocumentType('address_document_status')) {
                            $company->update(
                                [
                                    'address_document_status' => $company->present()
                                        ->getAddressDocumentStatus('analyzing'),
                                ]
                            );
                        }
                        if ($documentType == $company->present()->getDocumentType('contract_document_status')) {
                            $company->update(
                                [
                                    'contract_document_status' => $company->present()->getContractDocumentStatus('analyzing'),
                                ]
                            );
                        }
                    }
                }
            } catch (Exception $e) {
                Log::warning('RegisterController uploadDocuments');

                report($e);
                return false;
            }
        }
        return true;
    }
}


