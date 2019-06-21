<?php

namespace Modules\Companies\Http\Controllers;

use Exception;
use App\Entities\Company;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //        $companies = $this->getCompanyModel()
        //                          ->with('user')
        //                          ->where('user_id', auth()->user()->id)
        //                          ->get();
        //
        //        $companiesResource = CompanyResource::collection($companies);

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
    public function edit($encodedId)
    {
        $company = $this->getCompanyModel()
                        ->with('user')
                        ->find(current(Hashids::decode($encodedId)));

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
     * @return array
     */
    public function getBrasilianBanks()
    {

        $banks = [
            ['code' => '001', 'name' => 'Banco do Brasil'],
            ['code' => '003', 'name' => 'Banco da Amazônia'],
            ['code' => '004', 'name' => 'Banco do Nordeste'],
            ['code' => '021', 'name' => 'Banestes'],
            ['code' => '025', 'name' => 'Banco Alfa'],
            ['code' => '027', 'name' => 'Besc'],
            ['code' => '029', 'name' => 'Banerj'],
            ['code' => '031', 'name' => 'Banco Beg'],
            ['code' => '033', 'name' => 'Banco Santander Banespa'],
            ['code' => '036', 'name' => 'Banco Bem'],
            ['code' => '037', 'name' => 'Banpará'],
            ['code' => '038', 'name' => 'Banestado'],
            ['code' => '039', 'name' => 'BEP'],
            ['code' => '040', 'name' => 'Banco Cargill'],
            ['code' => '041', 'name' => 'Banrisul'],
            ['code' => '044', 'name' => 'BVA'],
            ['code' => '045', 'name' => 'Banco Opportunity'],
            ['code' => '047', 'name' => 'Banese'],
            ['code' => '062', 'name' => 'Hipercard'],
            ['code' => '063', 'name' => 'Ibibank'],
            ['code' => '065', 'name' => 'Lemon Bank'],
            ['code' => '066', 'name' => 'Banco Morgan Stanley Dean Witter'],
            ['code' => '069', 'name' => 'BPN Brasil'],
            ['code' => '070', 'name' => 'Banco de Brasília – BRB'],
            ['code' => '072', 'name' => 'Banco Rural'],
            ['code' => '073', 'name' => 'Banco Popular'],
            ['code' => '074', 'name' => 'Banco J. Safra'],
            ['code' => '075', 'name' => 'Banco CR2'],
            ['code' => '076', 'name' => 'Banco KDB'],
            ['code' => '096', 'name' => 'Banco BMF'],
            ['code' => '104', 'name' => 'Caixa Econômica Federal'],
            ['code' => '107', 'name' => 'Banco BBM'],
            ['code' => '116', 'name' => 'Banco Único'],
            ['code' => '151', 'name' => 'Nossa Caixa'],
            ['code' => '175', 'name' => 'Banco Finasa'],
            ['code' => '184', 'name' => 'Banco Itaú BBA'],
            ['code' => '204', 'name' => 'American Express Bank'],
            ['code' => '208', 'name' => 'Banco Pactual'],
            ['code' => '212', 'name' => 'Banco Matone'],
            ['code' => '213', 'name' => 'Banco Arbi'],
            ['code' => '214', 'name' => 'Banco Dibens'],
            ['code' => '217', 'name' => 'Banco Joh Deere'],
            ['code' => '218', 'name' => 'Banco Bonsucesso'],
            ['code' => '222', 'name' => 'Banco Calyon Brasil'],
            ['code' => '224', 'name' => 'Banco Fibra'],
            ['code' => '225', 'name' => 'Banco Brascan'],
            ['code' => '229', 'name' => 'Banco Cruzeiro'],
            ['code' => '230', 'name' => 'Unicard'],
            ['code' => '233', 'name' => 'Banco GE Capital'],
            ['code' => '237', 'name' => 'Bradesco'],
            ['code' => '241', 'name' => 'Banco Clássico'],
            ['code' => '243', 'name' => 'Banco Stock Máxima'],
            ['code' => '246', 'name' => 'Banco ABC Brasil'],
            ['code' => '248', 'name' => 'Banco Boavista Interatlântico'],
            ['code' => '249', 'name' => 'Investcred Unibanco'],
            ['code' => '250', 'name' => 'Banco Schahin'],
            ['code' => '252', 'name' => 'Fininvest'],
            ['code' => '254', 'name' => 'Paraná Banco'],
            ['code' => '263', 'name' => 'Banco Cacique'],
            ['code' => '265', 'name' => 'Banco Fator'],
            ['code' => '266', 'name' => 'Banco Cédula'],
            ['code' => '300', 'name' => 'Banco de la Nación Argentina'],
            ['code' => '318', 'name' => 'Banco BMG'],
            ['code' => '320', 'name' => 'Banco Industrial e Comercial'],
            ['code' => '356', 'name' => 'ABN Amro Real'],
            ['code' => '341', 'name' => 'Itau'],
            ['code' => '347', 'name' => 'Sudameris'],
            ['code' => '351', 'name' => 'Banco Santander'],
            ['code' => '353', 'name' => 'Banco Santander Brasil'],
            ['code' => '366', 'name' => 'Banco Societe Generale Brasil'],
            ['code' => '370', 'name' => 'Banco WestLB'],
            ['code' => '376', 'name' => 'JP Morgan'],
            ['code' => '389', 'name' => 'Banco Mercantil do Brasil'],
            ['code' => '394', 'name' => 'Banco Mercantil de Crédito'],
            ['code' => '399', 'name' => 'HSBC'],
            ['code' => '409', 'name' => 'Unibanco'],
            ['code' => '412', 'name' => 'Banco Capital'],
            ['code' => '422', 'name' => 'Banco Safra'],
            ['code' => '453', 'name' => 'Banco Rural'],
            ['code' => '456', 'name' => 'Banco Tokyo Mitsubishi UFJ'],
            ['code' => '464', 'name' => 'Banco Sumitomo Mitsui Brasileiro'],
            ['code' => '477', 'name' => 'Citibank'],
            ['code' => '479', 'name' => 'Itaubank (antigo Bank Boston)'],
            ['code' => '487', 'name' => 'Deutsche Bank'],
            ['code' => '488', 'name' => 'Banco Morgan Guaranty'],
            ['code' => '492', 'name' => 'Banco NMB Postbank'],
            ['code' => '494', 'name' => 'Banco la República Oriental del Uruguay'],
            ['code' => '495', 'name' => 'Banco La Provincia de Buenos Aires'],
            ['code' => '505', 'name' => 'Banco Credit Suisse'],
            ['code' => '600', 'name' => 'Banco Luso Brasileiro'],
            ['code' => '604', 'name' => 'Banco Industrial'],
            ['code' => '610', 'name' => 'Banco VR'],
            ['code' => '611', 'name' => 'Banco Paulista'],
            ['code' => '612', 'name' => 'Banco Guanabara'],
            ['code' => '613', 'name' => 'Banco Pecunia'],
            ['code' => '623', 'name' => 'Banco Panamericano'],
            ['code' => '626', 'name' => 'Banco Ficsa'],
            ['code' => '630', 'name' => 'Banco Intercap'],
            ['code' => '633', 'name' => 'Banco Rendimento'],
            ['code' => '634', 'name' => 'Banco Triângulo'],
            ['code' => '637', 'name' => 'Banco Sofisa'],
            ['code' => '638', 'name' => 'Banco Prosper'],
            ['code' => '643', 'name' => 'Banco Pine'],
            ['code' => '652', 'name' => 'Itaú Holding Financeira'],
            ['code' => '653', 'name' => 'Banco Indusval'],
            ['code' => '654', 'name' => 'Banco A.J. Renner'],
            ['code' => '655', 'name' => 'Banco Votorantim'],
            ['code' => '707', 'name' => 'Banco Daycoval'],
            ['code' => '719', 'name' => 'Banif'],
            ['code' => '721', 'name' => 'Banco Credibel'],
            ['code' => '734', 'name' => 'Banco Gerdau'],
            ['code' => '735', 'name' => 'Banco Pottencial'],
            ['code' => '738', 'name' => 'Banco Morada'],
            ['code' => '739', 'name' => 'Banco Galvão de Negócios'],
            ['code' => '740', 'name' => 'Banco Barclays'],
            ['code' => '741', 'name' => 'BRP'],
            ['code' => '743', 'name' => 'Banco Semear'],
            ['code' => '745', 'name' => 'Banco Citibank'],
            ['code' => '746', 'name' => 'Banco Modal'],
            ['code' => '747', 'name' => 'Banco Rabobank International'],
            ['code' => '748', 'name' => 'Banco Cooperativo Sicredi'],
            ['code' => '749', 'name' => 'Banco Simples'],
            ['code' => '751', 'name' => 'Dresdner Bank'],
            ['code' => '752', 'name' => 'BNP Paribas'],
            ['code' => '753', 'name' => 'Banco Comercial Uruguai'],
            ['code' => '755', 'name' => 'Banco Merrill Lynch'],
            ['code' => '756', 'name' => 'Banco Cooperativo do Brasil'],
            ['code' => '757', 'name' => 'KEB'],
        ];

        return $banks;
    }
}


