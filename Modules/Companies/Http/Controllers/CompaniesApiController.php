<?php

namespace Modules\Companies\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Companies\Http\Requests\CompanyCreateRequest;
use Modules\Companies\Http\Requests\CompanyUpdateRequest;
use Modules\Companies\Http\Requests\CompanyUploadDocumentRequest;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Companies\Transformers\CompanyCpfResource;
use Modules\Companies\Transformers\CompanyDocumentsResource;
use Modules\Companies\Transformers\CompanyResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Entities\Project;
use Modules\Core\Services\AmazonFileService;
use Modules\Core\Services\BankService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\CompanyServiceGetnet;
use Modules\Core\Services\FoxUtils;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class CompaniesController
 * @package Modules\Companies\Http\Controllers
 */
class CompaniesApiController extends Controller
{
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
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde',
                ],
                400
            );
        }
    }

    public function create()
    {
        return view('companies::create');
    }

    public function store(CompanyCreateRequest $request): JsonResponse
    {
        try {
            $companyModel = new Company();
            $requestData = $request->validated();

            $fantasyName = ($requestData['company_type'] == $companyModel->present()->getCompanyType('physical person'))
                ? auth()->user()->name : $requestData['fantasy_name'];

            $companyDocument = $requestData['company_type'] == $companyModel->present()->getCompanyType(
                'physical person'
            ) ? auth()->user()->document : FoxUtils::onlyNumbers($requestData['company_document']);

            $company = $companyModel->create(
                [
                    'user_id' => auth()->user()->account_owner_id,
                    'country' => $requestData["country"],
                    'fantasy_name' => $fantasyName,
                    'document' => $companyDocument,
                    'company_type' => $requestData['company_type'],
                    'account_type' => 1,
                ]
            );

            return response()->json(
                [
                    'message' => 'Empresa cadastrada com sucesso',
                    'idEncoded' => Hashids::encode($company->id)
                ],
                200
            );
        } catch (Exception $e) {
            report($e);

            return response()->json('erro', Response::HTTP_BAD_REQUEST);
        }
    }

    public function show($encodedId): JsonResponse
    {
        try {
            $companyModel = new Company();
            $bankService = new BankService();

            $company = $companyModel
                ->with('user', 'companyDocuments')
                ->find(current(Hashids::decode($encodedId)));

            if (!Gate::allows('edit', [$company])) {
                return response()->json(
                    [
                        'message' => 'Sem permissão para editar a empresa',
                    ],
                    400
                );
            }
            $banks = $bankService->getBanks($company->country ?? 'brazil');

            $companyResource = null;

            if ($company->company_type == $companyModel->present()->getCompanyType('juridical person')) {
                $companyResource = new CompanyResource($company);
            } elseif ($company->company_type == $companyModel->present()->getCompanyType('physical person')) {
                $companyResource = new CompanyCpfResource($company);
            }

            return response()->json(
                [
                    'company' => $companyResource,
                    'banks' => $banks,
                ],
                200
            );
        } catch (Exception $e) {
            report($e);

            return response()->json('erro', Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(CompanyUpdateRequest $request, $encodedId): JsonResponse
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
                $requestData['document'] = preg_replace("/[^0-9]/", "", $requestData['company_document']);
            }
            if ($company->country == 'brazil' && !empty($requestData['agency'])
                && strlen($requestData['agency']) == 3
            ) {
                $requestData['agency'] = substr_replace($requestData['agency'], '0', 0, 0);
            }

            $company->update($requestData);
            $companyService->getChangesUpdateBankData($company);

            return response()->json(['message' => 'Dados atualizados com sucesso'], Response::HTTP_OK);
        } catch (Exception $e) {
            report($e);

            return response()->json('erro', Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($encodedId): JsonResponse
    {
        try {
            $companyModel = new Company();
            $projectModel = new Project();

            $company = $companyModel->with('usersProjects')
                ->withCount(['transactions', 'usersProjects'])
                ->find(current(Hashids::decode($encodedId)));

            if (empty($company)) {
                return response()->json(
                    ['message' => 'Empresa não encontrada para remoção'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            if (!Gate::allows('destroy', [$company])) {
                return response()->json(
                    [
                        'message' => 'Sem permissão para remover a empresa',
                    ],
                    Response::HTTP_FORBIDDEN
                );
            }

            if ($company->transactions_count > 0) {
                return response()->json(
                    ['message' => 'Impossivel excluir, existem transações relacionadas a essa empresa!'],
                    Response::HTTP_BAD_REQUEST
                );
            }


            if ($company->users_projects_count > 0) {
                $projects = [];
                foreach ($company->usersProjects as $userProject) {
                    $projects = $projectModel->where('id', $userProject->project->id)
                        ->where('status', $projectModel->present()->getStatus('active'))
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
        } catch (Exception $e) {
            report($e);

            return response()->json('erro', Response::HTTP_BAD_REQUEST);
        }
    }

    public function uploadDocuments(CompanyUploadDocumentRequest $request): JsonResponse
    {
        try {
            $companyModel = new Company();

            $amazonFileService = app(AmazonFileService::class);

            $companyDocumentModel = new CompanyDocument();
            $dataForm = $request->validated();

            $company = $companyModel->find(current(Hashids::decode($dataForm['company_id'])));
            if (Gate::allows('uploadDocuments', [$company])) {
                $document = $request->file('file');

                $amazonFileService->setDisk('s3_documents');
                $amazonPath = $amazonFileService->uploadFile(
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
                        'document_url' => $amazonPath,
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

    public function openDocument(Request $request): JsonResponse
    {
        try {

            $amazonFileService = app(AmazonFileService::class);
            $data = $request->all();
            if (!empty($data['document_url'])) {
                $temporaryUrl = '';

                if (strstr($data['url'], 'amazonaws')) {
                    $amazonFileService->setDisk('s3_documents');
                    $temporaryUrl = $amazonFileService->getTemporaryUrlFile($data['url'], 180);
                }

                // Validacao
                if (empty($temporaryUrl)) {
                    return response()->json(['message' => 'Erro ao acessar documentos do usuário!'], 400);
                }

                return response()->json(['data' => $temporaryUrl], 200);
            }

            return response()->json(['message' => 'Erro ao acessar documento da empresa!'], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao acessar documento da empresa!'], 400);
        }
    }

    public function verify(): JsonResponse
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

    public function verifyCnpj(Request $request): JsonResponse
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

    public function consultCnpj(Request $request): JsonResponse
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

    public function updateOrder(Request $request): JsonResponse
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

    public function checkBraspagCompany(): JsonResponse
    {
        try {
            $user = auth()->user();
            $companyModel = new Company();
//            $columnName = FoxUtils::isProduction() ? 'braspag_merchant_id' : 'braspag_merchant_homolog_id';
//            $hasMerchantId = $companyModel->whereNotNull($columnName)
//                ->where('user_id', $user->account_owner_id)->exists();

            return response()->json(
                [
//                    'has_merchant_id' => $hasMerchantId,
//                    'env' => env("APP_ENV", "local"),
                ],
                Response::HTTP_UNAUTHORIZED
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao verificar empresas'], 400);
        }
    }

    public function checkStatementAvailable(): JsonResponse
    {
        try {
            $user = auth()->user();

            $hasSubsellerId = GatewaysCompaniesCredential::where('gateway_id',Gateway::GETNET_PRODUCTION_ID)
            ->where('gateway_status',GatewaysCompaniesCredential::GATEWAY_STATUS_APPROVED)
            ->with('company',function($query)use($user){
                $query->where('user_id', $user);
            })->first()->exists();

            return response()->json(
                [
                    'has_subseller_id' => $hasSubsellerId,
                    'env' => env("APP_ENV", "local"),
                ],
                200
            );
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao verificar empresas'], 400);
        }
    }

    public function updateTax(Request $request, $companyId): JsonResponse
    {
        try {
            if (FoxUtils::isEmpty($request->get('gateway_release_payment')) || FoxUtils::isEmpty($companyId)) {
                return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde!']);
            }

            $gatewayTax = [
                'plan-2' => [
                    'gateway_tax' => '6.9',
                    'gateway_release_money_days' => 2,
                ],
                'plan-15' => [
                    'gateway_tax' => '6.5',
                    'gateway_release_money_days' => 15,
                ],
                'plan-30' => [
                    'gateway_tax' => '5.9',
                    'gateway_release_money_days' => 30,
                ],
            ];

            if (!array_key_exists($request->get('gateway_release_payment'), $gatewayTax)) {
                return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde!'], 400);
            }

            $company = Company::find(current(Hashids::decode($companyId)));

            if (FoxUtils::isEmpty($company) || $company->getn) {
                return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde!'], 400);
            }
            $companyServiceGetnet = new CompanyServiceGetnet($company);
            $updateGetnet = $companyServiceGetnet->updateTaxCompanyGetnet(
                $gatewayTax[$request->get('gateway_release_payment')]
            );

            if (!$updateGetnet) {
                return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde!'], 400);
            }

            $companyUpdated = $company->update($gatewayTax[$request->get('gateway_release_payment')]);

            if ($companyUpdated) {
                return response()->json(
                    [
                        'message' => 'Taxa atualizado com sucesso!',
                        'data' => [
                            'new_gateway_tax' => $gatewayTax[$request->get('gateway_release_payment')]['gateway_tax']
                        ]
                    ],
                    200
                );
            }

            return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde!'], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde!'], 400);
        }
    }
}
