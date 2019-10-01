<?php

namespace Modules\Companies\Http\Controllers;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Modules\Companies\Http\Requests\CompanyCreateRequest;
use Modules\Companies\Http\Requests\CompanyUpdateRequest;
use Modules\Companies\Http\Requests\CompanyUploadDocumentRequest;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Companies\Transformers\CompanyResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Services\BankService;
use Modules\Core\Services\DigitalOceanFileService;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class CompaniesController
 * @package Modules\Companies\Http\Controllers
 */
class CompaniesApiController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        /** @var Company $companyModel */
        $companyModel = new Company();
        /** @var LengthAwarePaginator $companies */
        $companies = $companyModel->with('user')
                                  ->where('user_id', auth()->id())
                                  ->paginate();

        return CompanyResource::collection($companies);
    }

    /**
     * @return Factory|View
     */
    public function create()
    {

        return view('companies::create');
    }

    /**
     * @param CompanyCreateRequest $request
     * @return JsonResponse
     */
    public function store(CompanyCreateRequest $request)
    {
        try {
            /** @var Company $companyModel */
            $companyModel = new Company();
            $requestData  = $request->validated();
            /** @var Company $company */
            $company = $companyModel->newQuery()->create(
                [
                    'user_id'          => auth()->user()->id,
                    'country'          => $requestData["country"],
                    'fantasy_name'     => $requestData["fantasy_name"],
                    'company_document' => $requestData["company_document"],
                ]
            );

            return response()->json(
                [
                    'message'   => 'Dados atualizados com sucesso',
                    'idEncoded' => Hashids::encode($company->id),
                    //                    'redirect' => route('companies.edit', ['idEncoded' => Hashids::encode($company->id)]),
                ], Response::HTTP_OK
            );
        } catch (Exception $e) {
            Log::warning('Erro ao cadastrar empresa (CompaniesController - store)');
            report($e);

            return response()->json('erro', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $encodedId
     * @return Factory|JsonResponse|View
     */
    public function show($encodedId)
    {
        try {
            /** @var Company $companyModel */
            $companyModel = new Company();
            /** @var BankService $bankService */
            $bankService = new BankService();
            /** @var Company $company */
            $company = $companyModel
                ->with('user')
                ->find(current(Hashids::decode($encodedId)));
            if (Gate::allows('edit', [$company])) {
                $banks = $bankService->getBanks('BR');
                /** @var CompanyResource $companyResource */
                $companyResource = new CompanyResource($company);

                return response()->json(
                    [
                        'company' => $companyResource,
                        'banks'   => $banks,
                    ], Response::HTTP_OK
                );
            } else {
                return response()->json(
                    [
                        'message' => 'Sem permissão para editar a empresa',
                    ], Response::HTTP_FORBIDDEN
                );
            }
        } catch (Exception $e) {
            Log::warning('CompaniesController - edit - error');
            report($e);

            return response()->json('erro', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param CompanyUpdateRequest $request
     * @param $encodedId
     * @return JsonResponse
     */
    public function update(CompanyUpdateRequest $request, $encodedId)
    {
        try {
            /** @var Company $companyModel */
            $companyModel = new Company();
            $requestData  = $request->validated();
            /** @var Company $company */
            $company = $companyModel->newQuery()->find(current(Hashids::decode($encodedId)));
            if (Gate::allows('update', [$company])) {
                if (isset($requestData['company_document']) && $company->company_document != $requestData['company_document']) {
                    $company->bank_document_status = $companyModel->present()->getBankDocumentStatus('pending');
                }
                $requestData = array_filter($requestData);
                $company->update($requestData);

                return response()->json(['message' => 'Dados atualizados com sucesso'], Response::HTTP_OK);
            } else {
                return response()->json(['message' => 'Sem permissão para atualizar a empresa'], Response::HTTP_FORBIDDEN);
            }
        } catch (Exception $e) {
            Log::warning('CompaniesController - update - error');
            report($e);

            return response()->json('erro', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $encodedId
     * @return JsonResponse
     */
    public function destroy($encodedId)
    {
        try {
            /** @var Company $companyModel */
            $companyModel = new Company();
            /** @var Company $company */
            $company = $companyModel->newQuery()->withCount([
                                                                'transactions',
                                                                'usersProjects',
                                                            ])->find(current(Hashids::decode($encodedId)));
            if ($company) {
                if (Gate::allows('destroy', [$company])) {
                    if ($company->transactions_count > 0) {
                        return response()->json(['message' => 'Impossivel excluir, existem transações relacionadas a essa empresa!'], Response::HTTP_BAD_REQUEST);
                    } else if ($company->users_projects_count > 0) {
                        return response()->json(['message' => 'Impossivel excluir, existem projetos relacionadas a essa empresa!'], Response::HTTP_BAD_REQUEST);
                    } else {
                        $company->delete();

                        return response()->json(['message' => 'Empresa removida com sucesso'], Response::HTTP_OK);
                    }
                } else {
                    return response()->json(
                        [
                            'message' => 'Sem permissão para remover a empresa',
                        ], Response::HTTP_FORBIDDEN
                    );
                }
            } else {
                //empresa nao exsite
                return response()->json(['message' => 'Empresa não encontrada para remoção'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            Log::warning('CompaniesController - destroy - error');
            report($e);

            return response()->json('erro', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param CompanyUploadDocumentRequest $request
     * @return JsonResponse
     */
    public function uploadDocuments(CompanyUploadDocumentRequest $request)
    {
        try {
            /** @var Company $companyModel */
            $companyModel = new Company();
            /** @var DigitalOceanFileService $digitalOceanFileService */
            $digitalOceanFileService = app()->make(DigitalOceanFileService::class);
            /** @var CompanyDocument $companyDocumentModel */
            $companyDocumentModel = new CompanyDocument();
            $dataForm             = $request->validated();
            /** @var Company $company */
            $company = $companyModel->newQuery()->find(current(Hashids::decode($dataForm['company_id'])));
            if (Gate::allows('uploadDocuments', [$company])) {
                $document         = $request->file('file');
                $digitalOceanPath = $digitalOceanFileService->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->id) . '/companies/' . Hashids::encode($company->id) . '/private/documents', $document, null, null, 'private');
                $companyDocumentModel->newQuery()->create(
                    [
                        'company_id'         => $company->id,
                        'document_url'       => $digitalOceanPath,
                        'document_type_enum' => $dataForm["document_type"],
                        'status'             => null,
                    ]
                );
                if (($dataForm["document_type"] ?? '') == $company->present()
                                                                  ->getDocumentType('bank_document_status')) {
                    $company->update([
                                         'bank_document_status' => $company->present()
                                                                           ->getBankDocumentStatus('analyzing'),
                                     ]);
                }
                if (($dataForm["document_type"] ?? '') == $company->present()
                                                                  ->getDocumentType('address_document_status')) {
                    $company->update([
                                         'address_document_status' => $company->present()
                                                                              ->getAddressDocumentStatus('analyzing'),
                                     ]);
                }
                if (($dataForm["document_type"] ?? '') == $company->present()
                                                                  ->getDocumentType('contract_document_status')) {
                    $company->update([
                                         'contract_document_status' => $company->present()
                                                                               ->getContractDocumentStatus('analyzing'),
                                     ]);
                }

                return response()->json([
                                            'message' => 'Arquivo enviado com sucesso.',
                                            'data'    => [
                                                'bank_document_translate'     => [
                                                    'status'  => $company->bank_document_status,
                                                    'message' => Lang::get('definitions.enum.status.' . $company->present()
                                                                                                                ->getBankDocumentStatus($company->bank_document_status)),
                                                ],
                                                'address_document_translate'  => [
                                                    'status'  => $company->address_document_status,
                                                    'message' => Lang::get('definitions.enum.status.' . $company->present()
                                                                                                                ->getAddressDocumentStatus($company->address_document_status)),
                                                ],
                                                'contract_document_translate' => [
                                                    'status'  => $company->contract_document_status,
                                                    'message' => Lang::get('definitions.enum.status.' . $company->present()
                                                                                                                ->getContractDocumentStatus($company->contract_document_status)),
                                                ],
                                            ],
                                        ], Response::HTTP_OK);
            } else {
                return response()->json([
                                            'message' => 'Sem permissão para enviar documentos para a empresa',
                                        ], Response::HTTP_FORBIDDEN);
            }
        } catch (Exception $e) {
            Log::warning('ProfileController uploadDocuments');
            report($e);

            return response()->json(['message' => 'Não foi possivel enviar o arquivo.'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function getCompanies()
    {
        /** @var Company $companyModel */
        $companyModel = new Company();
        $companies    = $companyModel->newQuery()->where('user_id', auth()->user()->id)->get();

        return CompaniesSelectResource::collection($companies);
    }
}


