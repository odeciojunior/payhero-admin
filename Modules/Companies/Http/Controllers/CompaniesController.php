<?php

namespace Modules\Companies\Http\Controllers;

use App\Entities\CompanyDocument;
use Exception;
use App\Entities\Company;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    public function store(Request $request)
    {
        try {
            $requestData = $request->all();

            $requestData['user'] = auth()->user()->id;

            $company = $this->getCompanyModel()->create($requestData);

            return new CompanyResource($company);
        } catch (Exception $e) {
            Log::warning('Erro ao cadastrar empresa (CompaniesController - store)');
            report($e);

            return response()->json('erro');
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Throwable
     */
    public function getCreateForm(Request $request)
    {

        if ($request->country == 'brazil') {
            $view = view('companies::create_brasilian_company', [
                'bancos' => $this->getBankService()->getBanks('BR'),
            ]);
        } else {
            $view = view('companies::create_american_company');
        }

        return response()->json($view->render());
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

            return view('companies::edit', [
                'company' => json_decode(json_encode($companyResource)),
                'banks'   => $banks,
            ]);
        } catch (Exception $e) {
            Log::warning('CompaniesController - edit - error');
            report($e);

            return response()->json('erro');
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

            $requestData = array_filter($requestData);

            $company->update($requestData);

            return response()->json(['message' => 'Dados atualizados com sucesso'], 200);
        } catch (Exception $e) {
            Log::warning('CompaniesController - update - error');
            report($e);

            return response()->json('erro');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {

        Company::find($id)->delete();

        return redirect()->route('companies');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function details(Request $request)
    {

        $requestData = $request->all();

        $company = Company::find($requestData['id_empresa']);

        $modalBody = '';

        $modalBody .= "<div class='col-xl-12 col-lg-12'>";
        $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
        $modalBody .= "<thead>";
        $modalBody .= "</thead>";
        $modalBody .= "<tbody>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Status da conta bancária:</b></td>";
        if ($company->recipient_id != '')
            $modalBody .= "<td>Ativa</td>";
        else
            $modalBody .= "<td>Inativa</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>CNPJ:</b></td>";
        $modalBody .= "<td>" . $company->cnpj . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Nome fantasia:</b></td>";
        $modalBody .= "<td>" . $company->name . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Email:</b></td>";
        $modalBody .= "<td>" . $company->email . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Telefone:</b></td>";
        $modalBody .= "<td>" . $company->telefone . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>CEP:</b></td>";
        $modalBody .= "<td>" . $company->cep . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Estado:</b></td>";
        $modalBody .= "<td>" . $company->estado . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Cidade:</b></td>";
        $modalBody .= "<td>" . $company->cidade . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Bairro:</b></td>";
        $modalBody .= "<td>" . $company->bairro . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Rua:</b></td>";
        $modalBody .= "<td>" . $company->logradouro . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Número:</b></td>";
        $modalBody .= "<td>" . $company->numero . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Complemento:</b></td>";
        $modalBody .= "<td>" . $company->complemento . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "</thead>";
        $modalBody .= "</table>";
        $modalBody .= "</div>";
        $modalBody .= "</div>";

        return response()->json($modalBody);
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
                                     'bank_document_status' => $company->getEnum('bank_document_status', 'pending'),
                                 ]);
            }

            if (($dataForm["document_type"] ?? '') == $company->getEnum('document_type', 'address_document_status')) {
                $company->update([
                                     'address_document_status' => $company->getEnum('address_document_status', 'pending'),
                                 ]);
            }

            if (($dataForm["document_type"] ?? '') == $company->getEnum('document_type', 'contract_document_status')) {
                $company->update([
                                     'contract_document_status' => $company->getEnum('contract_document_status', 'pending'),
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


