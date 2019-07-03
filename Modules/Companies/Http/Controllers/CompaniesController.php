<?php

namespace Modules\Companies\Http\Controllers;

use App\Entities\CompanyDocument;
use Exception;
use App\Entities\Company;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
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
     * @var Company
     */
    private $companyModel;
    /**
     * @var DigitalOceanFileService
     */
    private $digitalOceanFileService;
    /**
     * @var CompanyDocument
     */
    private $companyDocumentModel;
    /**
     * @var BankService
     */
    private $bankService;

    /**
     * @return Company|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getCompanyModel()
    {
        if (!$this->companyModel) {
            $this->companyModel = app(Company::class);
        }

        return $this->companyModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDigitalOceanFileService()
    {
        if (!$this->digitalOceanFileService) {
            $this->digitalOceanFileService = app(DigitalOceanFileService::class);
        }

        return $this->digitalOceanFileService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getCompanyDocumentModel()
    {
        if (!$this->companyDocumentModel) {
            $this->companyDocumentModel = app(CompanyDocument::class);
        }

        return $this->companyDocumentModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getBankService()
    {
        if (!$this->bankService) {
            $this->bankService = app(BankService::class);
        }

        return $this->bankService;
    }

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
            $requestData = $request->validated();

            $company = $this->getCompanyModel()->create([
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
            $company = $this->getCompanyModel()
                            ->with('user')
                            ->find(current(Hashids::decode($encodedId)));

            $banks = $this->getBankService()->getBanks('BR');

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
            $requestData = $request->validated();

            $company = $this->getCompanyModel()
                            ->find(current(Hashids::decode($encodedId)));
            if ($company->company_document != $requestData['company_document']) {
                $company->bank_document_status = $this->getCompanyModel()->getEnum('bank_document_status', 'pending');
            }
            $requestData = array_filter($requestData);

            $company->update($requestData);

            return response()->json(['message' => 'Dados atualizados com sucesso'], 200);
        } catch (Exception $e) {
            Log::warning('CompaniesController - update - error');
            report($e);

            return response()->json('erro', 400);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($encodedId)
    {
        try {

            $company = $this->getCompanyModel()
                            ->find(current(Hashids::decode($encodedId)));
            if ($company) {
                //empresa existe
                $company->delete();
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
            $dataForm = $request->validated();
            $company  = $this->getCompanyModel()->find(current(Hashids::decode($dataForm['company_id'])));

            $document = $request->file('file');

            $digitalOceanPath = $this->getDigitalOceanFileService()
                                     ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->id) . '/companies/' . Hashids::encode($company->id) . '/private/documents', $document, null, null, 'private');

            $this->getCompanyDocumentModel()->create([
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
                                        'message'                     => 'Arquivo enviado com sucesso.',
                                        'bank_document_translate'     => $company->getEnum('bank_document_status', $company->bank_document_status, true),
                                        'address_document_translate'  => $company->getEnum('address_document_status', $company->address_document_status, true),
                                        'contract_document_translate' => $company->getEnum('contract_document_status', $company->contract_document_status, true),
                                    ], 200);
        } catch (Exception $e) {
            Log::warning('ProfileController uploadDocuments');
            report($e);

            return response()->json(['message' => 'Não foi possivel enviar o arquivo.'], 400);
        }
    }
}


