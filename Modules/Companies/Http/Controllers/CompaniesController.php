<?php

namespace Modules\Companies\Http\Controllers;

use App\Entities\CompanyDocument;
use Exception;
use App\Entities\Company;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Companies\Http\Requests\CompanyCreateFormRequest;
use Modules\Companies\Http\Requests\CompanyCreateRequest;
use Modules\Companies\Http\Requests\CompanyUpdateRequest;
use Modules\Companies\Http\Requests\CompanyUploadDocumentRequest;
use Modules\Core\Services\BankService;
use Modules\Core\Services\DigitalOceanFileService;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;
use Modules\Companies\Transformers\CompanyResource;

/**
 * Class CompaniesController
 * @package Modules\Companies\Http\Controllers
 */
class CompaniesController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('companies::index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('companies::create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|CompanyResource
     */
    public function store(CompanyCreateRequest $request)
    {
        try {
            $companyModel = new Company();

            $requestData = $request->validated();

            $company = $companyModel->create([
                                                 'user_id'          => auth()->user()->id,
                                                 'country'          => $requestData["country"],
                                                 'fantasy_name'     => $requestData["fantasy_name"],
                                                 'company_document' => $requestData["company_document"],
                                             ]);

            return response()->json([
                                        'message'  => 'Dados atualizados com sucesso',
                                        'redirect' => route('companies.edit', ['idEncoded' => Hashids::encode($company->id)]),
                                    ], 200);
        } catch (Exception $e) {
            Log::warning('Erro ao cadastrar empresa (CompaniesController - store)');
            report($e);

            return response()->json('erro', 400);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Throwable
     */
    public function getCreateForm(CompanyCreateFormRequest $request)
    {
        try {
            if ($request->country == 'usa') {
                $view = view('companies::create_american_company');
            } else {
                $view = view('companies::create_brazilian_company');
            }

            return response()->json($view->render());
        } catch (Exception $e) {
            Log::warning('Erro ao criar form de cadastro da empresa (CompaniesController - getCreateForm)');
            report($e);

            return response()->json('erro', 400);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($encodedId)
    {
        try {
            $companyModel = new Company();
            $bankService  = new BankService();

            $company = $companyModel
                ->with('user')
                ->find(current(Hashids::decode($encodedId)));

            if (Gate::allows('edit', [$company])) {
                $banks = $bankService->getBanks('BR');

                $companyResource = new CompanyResource($company);

                if ($company->country == 'usa') {
                    return view('companies::edit_usa', [
                        'company' => json_decode(json_encode($companyResource)),
                        'banks'   => $banks,
                    ]);
                } else {
                    return view('companies::edit_brazil', [
                        'company' => json_decode(json_encode($companyResource)),
                        'banks'   => $banks,
                    ]);
                }
            } else {
                return response()->json([
                                            'message' => 'Sem permissão para editar a empresa',
                                        ], 403);
            }
        } catch (Exception $e) {
            Log::warning('CompaniesController - edit - error');
            report($e);

            return response()->json('erro', 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CompanyUpdateRequest $request, $encodedId)
    {
        try {
            $companyModel = new Company();

            $requestData = $request->validated();

            $company = $companyModel
                ->find(current(Hashids::decode($encodedId)));

            if (Gate::allows('update', [$company])) {
                if (isset($requestData['company_document']) && $company->company_document != $requestData['company_document']) {
                    $company->bank_document_status = $companyModel->getEnum('bank_document_status', 'pending');
                }
                $requestData = array_filter($requestData);

                $company->update($requestData);

                return response()->json(['message' => 'Dados atualizados com sucesso'], 200);
            } else {
                return response()->json([
                                            'message' => 'Sem permissão para atualizar a empresa',
                                        ], 403);
            }
        } catch (Exception $e) {
            Log::warning('CompaniesController - update - error');
            report($e);

            return response()->json('erro', 400);
        }
    }

    /**
     * @param $idc
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($encodedId)
    {
        try {
            $companyModel = new Company();

            $company = $companyModel->withCount([
                                                    'transactions',
                                                    'usersProjects',
                                                ])->find(current(Hashids::decode($encodedId)));
            if ($company) {
                if (Gate::allows('destroy', [$company])) {
                    if ($company->transactions_count > 0) {
                        return response()->json(['message' => 'Impossivel excluir, existem transações relacionadas a essa empresa!'], 422);
                    } else if ($company->users_projects_count > 0) {
                        return response()->json(['message' => 'Impossivel excluir, existem projetos relacionadas a essa empresa!'], 422);
                    } else {
                        $company->delete();
                    }
                } else {
                    return response()->json([
                                                'message' => 'Sem permissão para remover a empresa',
                                            ], 403);
                }
            } else {
                //empresa nao exsite
                return response()->json(['message' => 'Empresa não encontrada para remoção'], 422);
            }

            return response()->json(['message' => 'Empresa removida com sucesso'], 200);
        } catch (Exception $e) {
            Log::warning('CompaniesController - destroy - error');
            report($e);

            return response()->json('erro', 400);
        }
    }

    /**
     * @param CompanyUpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadDocuments(CompanyUploadDocumentRequest $request)
    {
        try {
            $companyModel            = new Company();
            $digitalOceanFileService = app(DigitalOceanFileService::class);
            $companyDocumentModel    = new CompanyDocument();

            $dataForm = $request->validated();
            $company  = $companyModel->find(current(Hashids::decode($dataForm['company_id'])));

            if (Gate::allows('uploadDocuments', [$company])) {
                $document = $request->file('file');

                $digitalOceanPath = $digitalOceanFileService->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->id) . '/companies/' . Hashids::encode($company->id) . '/private/documents', $document, null, null, 'private');

                $companyDocumentModel->create([
                                                  'company_id'         => $company->id,
                                                  'document_url'       => $digitalOceanPath,
                                                  'document_type_enum' => $dataForm["document_type"],
                                                  'status'             => null,
                                              ]);

                if (($dataForm["document_type"] ?? '') == $company->getEnum('document_type', 'bank_document_status')) {
                    $company->update([
                                         'bank_document_status' => $company->getEnum('bank_document_status', 'analyzing'),
                                     ]);
                }

                if (($dataForm["document_type"] ?? '') == $company->getEnum('document_type', 'address_document_status')) {
                    $company->update([
                                         'address_document_status' => $company->getEnum('address_document_status', 'analyzing'),
                                     ]);
                }

                if (($dataForm["document_type"] ?? '') == $company->getEnum('document_type', 'contract_document_status')) {
                    $company->update([
                                         'contract_document_status' => $company->getEnum('contract_document_status', 'analyzing'),
                                     ]);
                }

                return response()->json([
                                            'message' => 'Arquivo enviado com sucesso.',
                                            'data'    => [
                                                'bank_document_translate'     => [
                                                    'status'  => $company->bank_document_status,
                                                    'message' => $company->getEnum('bank_document_status', $company->bank_document_status, true),
                                                ],
                                                'address_document_translate'  => [
                                                    'status'  => $company->address_document_status,
                                                    'message' => $company->getEnum('address_document_status', $company->address_document_status, true),
                                                ],
                                                'contract_document_translate' => [
                                                    'status'  => $company->contract_document_status,
                                                    'message' => $company->getEnum('contract_document_status', $company->contract_document_status, true),
                                                ],
                                            ],
                                        ], 200);
            } else {
                return response()->json([
                                            'message' => 'Sem permissão para enviar documentos para a empresa',
                                        ], 403);
            }
        } catch (Exception $e) {
            Log::warning('ProfileController uploadDocuments');
            report($e);

            return response()->json(['message' => 'Não foi possivel enviar o arquivo.'], 400);
        }
    }
}


