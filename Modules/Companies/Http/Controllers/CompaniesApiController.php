<?php

namespace Modules\Companies\Http\Controllers;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Companies\Transformers\CompanyDocumentsResource;
use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Project;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\GetnetService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;
use Illuminate\Contracts\View\Factory;
use Modules\Core\Services\BankService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Entities\CompanyDocument;
use Symfony\Component\HttpFoundation\Response;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Companies\Transformers\CompanyResource;
use Modules\Companies\Transformers\CompanyCpfResource;
use Modules\Companies\Http\Requests\CompanyCreateRequest;
use Modules\Companies\Http\Requests\CompanyUpdateRequest;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Companies\Http\Requests\CompanyUploadDocumentRequest;

/**
 * Class CompaniesController
 * @package Modules\Companies\Http\Controllers
 */
class CompaniesApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $companyService = new CompanyService();

            $paginate = true;
            if ($request->has('select') && $request->input('select')) {
                $paginate = false;
            }

            return $companyService->getCompaniesUser($paginate);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados companies CompaniesApiController - index');
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde',
                ],
                400
            );
        }
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
            $companyModel = new Company();
            $requestData = $request->validated();

            $company = $companyModel->create(
                [
                    'user_id' => auth()->user()->account_owner_id,
                    'country' => $requestData["country"],
                    'fantasy_name' => ($requestData['company_type'] == $companyModel->present()
                            ->getCompanyType('physical person')) ? auth()->user()->name : $requestData['fantasy_name'],
                    'company_document' => ($requestData['company_type'] == $companyModel->present()
                            ->getCompanyType('physical person')) ? auth()->user()->document : preg_replace(
                        "/[^0-9]/",
                        "",
                        $requestData['company_document']
                    ),
                    'company_type' => $requestData['company_type'],
                ]
            );

            return response()->json(
                [
                    'message' => 'Empresa cadastrada com sucesso',
                    'idEncoded' => Hashids::encode($company->id),
                ],
                Response::HTTP_OK
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
            $companyModel = new Company();

            $bankService = new BankService();

            $company = $companyModel
                ->with('user', 'companyDocuments')
                ->find(current(Hashids::decode($encodedId)));

            if (Gate::allows('edit', [$company])) {
                $banks = $bankService->getBanks($company->country ?? 'brazil');

                $companyResource = null;
                if ($company->company_type == $companyModel->present()->getCompanyType('juridical person')) {
                    $companyResource = new CompanyResource($company);
                } else {
                    if ($company->company_type == $companyModel->present()->getCompanyType('physical person')) {
                        $companyResource = new CompanyCpfResource($company);
                    }
                }

                return response()->json(
                    [
                        'company' => $companyResource,
                        'banks' => $banks,
                    ],
                    Response::HTTP_OK
                );
            } else {
                return response()->json(
                    [
                        'message' => 'Sem permissão para editar a empresa',
                    ],
                    Response::HTTP_FORBIDDEN
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
            $companyModel = new Company();
            $companyService = new CompanyService();

            $requestData = $request->validated();
            $company = $companyModel->find(current(Hashids::decode($encodedId)));

            if (!Gate::allows('update', [$company])) {
                return response()->json(
                    ['message' => 'Sem permissão para atualizar a empresa'],
                    Response::HTTP_FORBIDDEN
                );
            }

            if (!empty($requestData['country']) && $requestData['country'] == 'brazil' && !empty($requestData['support_telephone'])) {
                $requestData['support_telephone'] = '+' . FoxUtils::onlyNumbers($requestData['support_telephone']);
            }
            if (!empty($requestData['company_document'])) {
                $requestData['company_document'] = preg_replace("/[^0-9]/", "", $requestData['company_document']);
            }
            if ($company->country == 'brazil' && !empty($requestData['agency']) && strlen(
                    $requestData['agency']
                ) == 3) {
                $requestData['agency'] = substr_replace($requestData['agency'], '0', 0, 0);
            }
            if (!empty($requestData['patrimony'])) {
                $requestData['patrimony'] = preg_replace("/[^0-9]/", "", $requestData['patrimony']);
            }
            if (!empty($requestData['social_value'])) {
                $requestData['social_value'] = preg_replace("/[^0-9]/", "", $requestData['social_value']);
            }
            if (!empty($requestData['monthly_gross_income'])) {
                $requestData['monthly_gross_income'] = FoxUtils::onlyNumbers($requestData['monthly_gross_income']);
            }
            $company->update($requestData);
            $dataUpdate = $companyService->getChangesUpdateBankData($company);

            if (empty($company->subseller_getnet_id)) {
                $companyService->createCompanyGetnet($company);
            } elseif ($dataUpdate == true || $company->getnet_status != 'aprovado') {
                $getnetService = new GetnetService();

                if ($company->present()->getCompanyType($company->company_type) == 'physical person') {
                    $getnetService->updatePfCompany($company);
                } else {
                    $getnetService->updatePjCompany($company);
                }
            }


            return response()->json(['message' => 'Dados atualizados com sucesso'], Response::HTTP_OK);
        } catch (Exception $e) {
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
            $companyModel = new Company();
            $projectModel = new Project();

            $company = $companyModel->with('usersProjects')->withCount(
                [
                    'transactions',
                    'usersProjects',
                ]
            )
                ->find(current(Hashids::decode($encodedId)));
            if ($company) {
                if (Gate::allows('destroy', [$company])) {
                    if ($company->transactions_count > 0) {
                        return response()->json(
                            ['message' => 'Impossivel excluir, existem transações relacionadas a essa empresa!'],
                            Response::HTTP_BAD_REQUEST
                        );
                    } else {
                        if ($company->users_projects_count > 0) {
                            $projects = [];
                            foreach ($company->usersProjects as $userProject) {
                                $projects = $projectModel->where('id', $userProject->project->id)
                                    ->where(
                                        'status',
                                        $projectModel->present()
                                            ->getStatus('active')
                                    )
                                    ->get();
                            }

                            if (count($projects) > 0) {
                                return response()->json(
                                    ['message' => 'Impossivel excluir, existem projetos relacionadas a essa empresa!'],
                                    Response::HTTP_BAD_REQUEST
                                );
                            } else {
                                $company->delete();

                                return response()->json(
                                    ['message' => 'Empresa removida com sucesso'],
                                    Response::HTTP_OK
                                );
                            }
                        } else {
                            $company->delete();

                            return response()->json(['message' => 'Empresa removida com sucesso'], Response::HTTP_OK);
                        }
                    }
                } else {
                    return response()->json(
                        [
                            'message' => 'Sem permissão para remover a empresa',
                        ],
                        Response::HTTP_FORBIDDEN
                    );
                }
            } else {
                return response()->json(
                    ['message' => 'Empresa não encontrada para remoção'],
                    Response::HTTP_BAD_REQUEST
                );
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
            $companyModel = new Company();

            $digitalOceanFileService = app()->make(DigitalOceanFileService::class);

            $companyDocumentModel = new CompanyDocument();
            $dataForm = $request->validated();

            $company = $companyModel->find(current(Hashids::decode($dataForm['company_id'])));
            if (Gate::allows('uploadDocuments', [$company])) {
                $document = $request->file('file');
                $digitalOceanPath = $digitalOceanFileService->uploadFile(
                    'uploads/user/' . Hashids::encode(
                        auth()->user()->account_owner_id
                    ) . '/companies/' . Hashids::encode($company->id) . '/private/documents',
                    $document,
                    null,
                    null,
                    'private'
                );

                $documentType = $companyDocumentModel->present()->getDocumentType($dataForm["document_type"]);
                if (empty($documentType)) {
                    return response()->json(
                        ['message' => 'Não foi possivel enviar o arquivo.'],
                        Response::HTTP_BAD_REQUEST
                    );
                }

                $companyDocumentModel->create(
                    [
                        'company_id' => $company->id,
                        'document_url' => $digitalOceanPath,
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
                if ($documentType == $company->present()
                        ->getDocumentType('address_document_status')
                ) {
                    $company->update(
                        [
                            'address_document_status' => $company->present()
                                ->getAddressDocumentStatus('analyzing'),
                        ]
                    );
                }
                if ($documentType == $company->present()
                        ->getDocumentType('contract_document_status')
                ) {
                    $company->update(
                        [
                            'contract_document_status' => $company->present()
                                ->getContractDocumentStatus('analyzing'),
                        ]
                    );
                }

                return response()->json(
                    [
                        'message' => 'Arquivo enviado com sucesso.',
                    ],
                    Response::HTTP_OK
                );
            } else {
                return response()->json(
                    [
                        'message' => 'Sem permissão para enviar documentos para a empresa',
                    ],
                    Response::HTTP_FORBIDDEN
                );
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function openDocument(Request $request)
    {
        try {
            $digitalOceanFileService = app(DigitalOceanFileService::class);
            $data = $request->all();
            if (!empty($data['document_url'])) {
                $temporaryUrl = $digitalOceanFileService->getTemporaryUrlFile($data['document_url'], 180);

                return response()->json(['data' => $temporaryUrl], 200);
            }

            return response()->json(['message' => 'Erro ao acessar documento da empresa!'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao acessar documento da empresa CompaniesApiController - openDocument');
            report($e);
        }
    }

    /**
     * @return JsonResponse
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function verify()
    {
        $companyModel = new Company();
        $company = $companyModel->where(
            [
                ['user_id', auth()->user()->account_owner_id],
                [
                    'company_type',
                    $companyModel->present()->getCompanyType('physical person'),
                ],
            ]
        )->first();
        if (!empty($company)) {
            return response()->json(['has_physical_company' => 'true']);
        } else {
            return response()->json(['has_physical_company' => 'false']);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyCnpj(Request $request)
    {
        $data = $request->all();
        $companyService = new CompanyService();
        $cnpj = $companyService->verifyCnpj($data['company_document']);
        if ($cnpj) {
            return response()->json(
                [
                    'cnpj_exist' => 'true',
                    'message' => 'Esse CNPJ já está cadastrado na plataforma',
                ]
            );
        } else {
            return response()->json(
                [
                    'cnpj_exist' => 'false',
                ]
            );
        }
    }

    public function getDocuments(Request $request, $companyId)
    {
        try {
            if (!empty($companyId) && !empty($request->input('document_type'))) {
                $companyDocumentModel = new CompanyDocument();
                $companyDocuments = $companyDocumentModel->where('company_id', current(Hashids::decode($companyId)));

                if (!empty($request->input('document_type'))) {
                    $companyDocuments->where(
                        'document_type_enum',
                        $companyDocumentModel->present()
                            ->getDocumentType($request->input('document_type'))
                    );
                }

                return CompanyDocumentsResource::collection($companyDocuments->get());
            } else {
                return response()->json(
                    [
                        'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                ],
                400
            );
        }
    }

    public function consultCnpj(Request $request)
    {
        try {
            if (!empty($request->input('cnpj'))) {
                $companyService = new CompanyService();
                $companyGet = $companyService->getNameCompanyByApiCNPJ($request->input('cnpj'));
                if (!empty($companyGet['nome'])) {
                    return response()->json(['name' => $companyGet['nome']], 200);
                }
            }

            return response()->json(['message' => 'Erro ao buscar CNPJ'], 400);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao buscar CNPJ'], 400);
        }
    }

    public function updateOrder(Request $request)
    {
        try {
            $orders = $request->input('order');
            $page = $request->page ?? 1;
            $paginate = $request->paginate ?? 100;
            $initOrder = ($page * $paginate) - $paginate + 1;

            $companyIds = [];

            foreach ($orders as $order) {
                $companyIds[] = current(Hashids::decode($order));
            }

            $companies = Company::whereIn('id', collect($companyIds))
                ->where('user_id', auth()->user()->account_owner_id)
                ->get();

            foreach ($companyIds as $value) {
                $company = $companies->firstWhere('id', $value);
                $company->update(['order_priority' => $initOrder]);
                $initOrder++;
            }

            return response()->json(['message' => 'Ordenação atualizada com sucesso'], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao atualizar ordenação'], 400);
        }
    }
}
