<?php

namespace Modules\Companies\Http\Controllers;

use Exception;
use App\Entities\Company;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Modules\Companies\Transformers\CompanyResource;

/**
 * Class CompaniesController
 * @package Modules\Companies\Http\Controllers
 */
class CompaniesApiController extends Controller
{
    /**
     * @var Company
     */
    private $companyModel;

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
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $companies = $this->getCompanyModel()
                          ->with('user')
                          ->where('user_id', auth()->user()->id)
                          ->paginate(15);

        return CompanyResource::collection($companies);
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
                'bancos' => $this->getBrasilianBanks(),
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
    public function edit($id)
    {

        $company = Company::find($id);

        $banks = $this->getBrasilianBanks();

        return view('companies::edit', [
            'company' => $company,
            'banks'   => $banks,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {

        $requestData = $request->all();

        $company = Company::find($requestData['id']);
        $company->update($requestData);

        return redirect()->route('companies');
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
     * @return mixed
     * @throws Exception
     */
    public function getCompaniesData()
    {

        $companies = DB::table('companies as company')
                       ->select([
                                    'company.id',
                                    'company.cnpj',
                                    'company.fantasy_name',
                                ]);

        if (!auth()->user()->hasRole('administrador geral')) {
            $companies = $companies->where('user', auth()->user()->id);
        }

        return Datatables::of($companies)
                         ->editColumn('cnpj', function($company) {
                             if (strlen($company->cnpj) == '14')
                                 return vsprintf("%s%s.%s%s%s.%s%s%s/%s%s%s%s-%s%s", str_split($company->cnpj));
                             else if (strlen($company->cnpj) == '11')
                                 return vsprintf("%s%s%s.%s%s%s.%s%s%s-%s%s", str_split($company->cnpj));

                             return $company->cnpj;
                         })
                         ->addColumn('detalhes', function($company) {
                             return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_empresa' data-placement='top' data-toggle='tooltip' title='Detalhes' empresa='" . $company->id . "'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/empresas/editar/$company->id' class='btn btn-outline btn-primary editar_empresa' data-placement='top' data-toggle='tooltip' title='Editar' empresa='" . $company->id . "'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_empresa' data-placement='top' data-toggle='tooltip' title='Excluir' empresa='" . $company->id . "'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
                         })
                         ->rawColumns(['detalhes'])
                         ->make(true);
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
        $modalBody .= "<td>" . $company->nome . "</td>";
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
     * @return array
     */
    public function getBrasilianBanks()
    {

        $banks = [
            ['codigo' => '001', 'nome' => 'Banco do Brasil'],
            ['codigo' => '003', 'nome' => 'Banco da Amazônia'],
            ['codigo' => '004', 'nome' => 'Banco do Nordeste'],
            ['codigo' => '021', 'nome' => 'Banestes'],
            ['codigo' => '025', 'nome' => 'Banco Alfa'],
            ['codigo' => '027', 'nome' => 'Besc'],
            ['codigo' => '029', 'nome' => 'Banerj'],
            ['codigo' => '031', 'nome' => 'Banco Beg'],
            ['codigo' => '033', 'nome' => 'Banco Santander Banespa'],
            ['codigo' => '036', 'nome' => 'Banco Bem'],
            ['codigo' => '037', 'nome' => 'Banpará'],
            ['codigo' => '038', 'nome' => 'Banestado'],
            ['codigo' => '039', 'nome' => 'BEP'],
            ['codigo' => '040', 'nome' => 'Banco Cargill'],
            ['codigo' => '041', 'nome' => 'Banrisul'],
            ['codigo' => '044', 'nome' => 'BVA'],
            ['codigo' => '045', 'nome' => 'Banco Opportunity'],
            ['codigo' => '047', 'nome' => 'Banese'],
            ['codigo' => '062', 'nome' => 'Hipercard'],
            ['codigo' => '063', 'nome' => 'Ibibank'],
            ['codigo' => '065', 'nome' => 'Lemon Bank'],
            ['codigo' => '066', 'nome' => 'Banco Morgan Stanley Dean Witter'],
            ['codigo' => '069', 'nome' => 'BPN Brasil'],
            ['codigo' => '070', 'nome' => 'Banco de Brasília – BRB'],
            ['codigo' => '072', 'nome' => 'Banco Rural'],
            ['codigo' => '073', 'nome' => 'Banco Popular'],
            ['codigo' => '074', 'nome' => 'Banco J. Safra'],
            ['codigo' => '075', 'nome' => 'Banco CR2'],
            ['codigo' => '076', 'nome' => 'Banco KDB'],
            ['codigo' => '096', 'nome' => 'Banco BMF'],
            ['codigo' => '104', 'nome' => 'Caixa Econômica Federal'],
            ['codigo' => '107', 'nome' => 'Banco BBM'],
            ['codigo' => '116', 'nome' => 'Banco Único'],
            ['codigo' => '151', 'nome' => 'Nossa Caixa'],
            ['codigo' => '175', 'nome' => 'Banco Finasa'],
            ['codigo' => '184', 'nome' => 'Banco Itaú BBA'],
            ['codigo' => '204', 'nome' => 'American Express Bank'],
            ['codigo' => '208', 'nome' => 'Banco Pactual'],
            ['codigo' => '212', 'nome' => 'Banco Matone'],
            ['codigo' => '213', 'nome' => 'Banco Arbi'],
            ['codigo' => '214', 'nome' => 'Banco Dibens'],
            ['codigo' => '217', 'nome' => 'Banco Joh Deere'],
            ['codigo' => '218', 'nome' => 'Banco Bonsucesso'],
            ['codigo' => '222', 'nome' => 'Banco Calyon Brasil'],
            ['codigo' => '224', 'nome' => 'Banco Fibra'],
            ['codigo' => '225', 'nome' => 'Banco Brascan'],
            ['codigo' => '229', 'nome' => 'Banco Cruzeiro'],
            ['codigo' => '230', 'nome' => 'Unicard'],
            ['codigo' => '233', 'nome' => 'Banco GE Capital'],
            ['codigo' => '237', 'nome' => 'Bradesco'],
            ['codigo' => '241', 'nome' => 'Banco Clássico'],
            ['codigo' => '243', 'nome' => 'Banco Stock Máxima'],
            ['codigo' => '246', 'nome' => 'Banco ABC Brasil'],
            ['codigo' => '248', 'nome' => 'Banco Boavista Interatlântico'],
            ['codigo' => '249', 'nome' => 'Investcred Unibanco'],
            ['codigo' => '250', 'nome' => 'Banco Schahin'],
            ['codigo' => '252', 'nome' => 'Fininvest'],
            ['codigo' => '254', 'nome' => 'Paraná Banco'],
            ['codigo' => '263', 'nome' => 'Banco Cacique'],
            ['codigo' => '265', 'nome' => 'Banco Fator'],
            ['codigo' => '266', 'nome' => 'Banco Cédula'],
            ['codigo' => '300', 'nome' => 'Banco de la Nación Argentina'],
            ['codigo' => '318', 'nome' => 'Banco BMG'],
            ['codigo' => '320', 'nome' => 'Banco Industrial e Comercial'],
            ['codigo' => '356', 'nome' => 'ABN Amro Real'],
            ['codigo' => '341', 'nome' => 'Itau'],
            ['codigo' => '347', 'nome' => 'Sudameris'],
            ['codigo' => '351', 'nome' => 'Banco Santander'],
            ['codigo' => '353', 'nome' => 'Banco Santander Brasil'],
            ['codigo' => '366', 'nome' => 'Banco Societe Generale Brasil'],
            ['codigo' => '370', 'nome' => 'Banco WestLB'],
            ['codigo' => '376', 'nome' => 'JP Morgan'],
            ['codigo' => '389', 'nome' => 'Banco Mercantil do Brasil'],
            ['codigo' => '394', 'nome' => 'Banco Mercantil de Crédito'],
            ['codigo' => '399', 'nome' => 'HSBC'],
            ['codigo' => '409', 'nome' => 'Unibanco'],
            ['codigo' => '412', 'nome' => 'Banco Capital'],
            ['codigo' => '422', 'nome' => 'Banco Safra'],
            ['codigo' => '453', 'nome' => 'Banco Rural'],
            ['codigo' => '456', 'nome' => 'Banco Tokyo Mitsubishi UFJ'],
            ['codigo' => '464', 'nome' => 'Banco Sumitomo Mitsui Brasileiro'],
            ['codigo' => '477', 'nome' => 'Citibank'],
            ['codigo' => '479', 'nome' => 'Itaubank (antigo Bank Boston)'],
            ['codigo' => '487', 'nome' => 'Deutsche Bank'],
            ['codigo' => '488', 'nome' => 'Banco Morgan Guaranty'],
            ['codigo' => '492', 'nome' => 'Banco NMB Postbank'],
            ['codigo' => '494', 'nome' => 'Banco la República Oriental del Uruguay'],
            ['codigo' => '495', 'nome' => 'Banco La Provincia de Buenos Aires'],
            ['codigo' => '505', 'nome' => 'Banco Credit Suisse'],
            ['codigo' => '600', 'nome' => 'Banco Luso Brasileiro'],
            ['codigo' => '604', 'nome' => 'Banco Industrial'],
            ['codigo' => '610', 'nome' => 'Banco VR'],
            ['codigo' => '611', 'nome' => 'Banco Paulista'],
            ['codigo' => '612', 'nome' => 'Banco Guanabara'],
            ['codigo' => '613', 'nome' => 'Banco Pecunia'],
            ['codigo' => '623', 'nome' => 'Banco Panamericano'],
            ['codigo' => '626', 'nome' => 'Banco Ficsa'],
            ['codigo' => '630', 'nome' => 'Banco Intercap'],
            ['codigo' => '633', 'nome' => 'Banco Rendimento'],
            ['codigo' => '634', 'nome' => 'Banco Triângulo'],
            ['codigo' => '637', 'nome' => 'Banco Sofisa'],
            ['codigo' => '638', 'nome' => 'Banco Prosper'],
            ['codigo' => '643', 'nome' => 'Banco Pine'],
            ['codigo' => '652', 'nome' => 'Itaú Holding Financeira'],
            ['codigo' => '653', 'nome' => 'Banco Indusval'],
            ['codigo' => '654', 'nome' => 'Banco A.J. Renner'],
            ['codigo' => '655', 'nome' => 'Banco Votorantim'],
            ['codigo' => '707', 'nome' => 'Banco Daycoval'],
            ['codigo' => '719', 'nome' => 'Banif'],
            ['codigo' => '721', 'nome' => 'Banco Credibel'],
            ['codigo' => '734', 'nome' => 'Banco Gerdau'],
            ['codigo' => '735', 'nome' => 'Banco Pottencial'],
            ['codigo' => '738', 'nome' => 'Banco Morada'],
            ['codigo' => '739', 'nome' => 'Banco Galvão de Negócios'],
            ['codigo' => '740', 'nome' => 'Banco Barclays'],
            ['codigo' => '741', 'nome' => 'BRP'],
            ['codigo' => '743', 'nome' => 'Banco Semear'],
            ['codigo' => '745', 'nome' => 'Banco Citibank'],
            ['codigo' => '746', 'nome' => 'Banco Modal'],
            ['codigo' => '747', 'nome' => 'Banco Rabobank International'],
            ['codigo' => '748', 'nome' => 'Banco Cooperativo Sicredi'],
            ['codigo' => '749', 'nome' => 'Banco Simples'],
            ['codigo' => '751', 'nome' => 'Dresdner Bank'],
            ['codigo' => '752', 'nome' => 'BNP Paribas'],
            ['codigo' => '753', 'nome' => 'Banco Comercial Uruguai'],
            ['codigo' => '755', 'nome' => 'Banco Merrill Lynch'],
            ['codigo' => '756', 'nome' => 'Banco Cooperativo do Brasil'],
            ['codigo' => '757', 'nome' => 'KEB'],
        ];

        return $banks;
    }
}


