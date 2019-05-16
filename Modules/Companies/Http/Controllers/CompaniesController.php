<?php

namespace Modules\Companies\Http\Controllers;

use Auth;
use App\Entities\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class CompaniesController extends Controller {

    public function index() {

        return view('companies::index'); 
    }

    public function create() {

        return view('companies::create');
    }

    public function store(Request $request){

        $requestData = $request->all();

        $requestData['user'] = \Auth::user()->id;

        $company = Company::create($requestData);

        return redirect()->route('companies');
    }

    public function getCreateForm(Request $request){

        if($request->country == 'brazil'){
            $view = view('companies::create_brasilian_company',[
                'bancos' => $this->getBrasilianBanks()
            ]);
        }
        else{
            $view = view('companies::create_american_company');
        }

        return response()->json($view->render());
    }

    public function edit($id){

        $company = Company::find($id);

        $banks = $this->getBrasilianBanks();

        return view('companies::edit',[
            'company' => $company,
            'banks'   => $banks
        ]);
    }

    public function update(Request $request){

        $requestData = $request->all();

        $company = Company::find($requestData['id']);
        $company->update($requestData);

        return redirect()->route('companies');
    }

    public function delete($id){

        Company::find($id)->delete();

        return redirect()->route('companies');

    }

    public function getCompaniesData() {

        $companies = \DB::table('companies as company')
            ->select([
                'company.id',
                'company.cnpj',
                'company.fantasy_name',
        ]); 

        if(!\Auth::user()->hasRole('administrador geral')){
            $companies = $companies->where('user',\Auth::user()->id);
        }

        return Datatables::of($companies)
        ->editColumn('cnpj', function ($company) {
            if(strlen($company->cnpj) == '14')
                return vsprintf("%s%s.%s%s%s.%s%s%s/%s%s%s%s-%s%s", str_split($company->cnpj));
            elseif(strlen($company->cnpj) == '11')
                return vsprintf("%s%s%s.%s%s%s.%s%s%s-%s%s", str_split($company->cnpj));
            return $company->cnpj;
        })
        ->addColumn('detalhes', function ($company) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_empresa' data-placement='top' data-toggle='tooltip' title='Detalhes' empresa='".$company->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/empresas/editar/$company->id' class='btn btn-outline btn-primary editar_empresa' data-placement='top' data-toggle='tooltip' title='Editar' empresa='".$company->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_empresa' data-placement='top' data-toggle='tooltip' title='Excluir' empresa='".$company->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function details(Request $request){

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
        if($company->recipient_id != '')
            $modalBody .= "<td>Ativa</td>";
        else
            $modalBody .= "<td>Inativa</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>CNPJ:</b></td>";
        $modalBody .= "<td>".$company->cnpj."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Nome fantasia:</b></td>";
        $modalBody .= "<td>".$company->nome."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Email:</b></td>";
        $modalBody .= "<td>".$company->email."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Telefone:</b></td>";
        $modalBody .= "<td>".$company->telefone."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>CEP:</b></td>";
        $modalBody .= "<td>".$company->cep."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Estado:</b></td>";
        $modalBody .= "<td>".$company->estado."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Cidade:</b></td>";
        $modalBody .= "<td>".$company->cidade."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Bairro:</b></td>";
        $modalBody .= "<td>".$company->bairro."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Rua:</b></td>";
        $modalBody .= "<td>".$company->logradouro."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Número:</b></td>";
        $modalBody .= "<td>".$company->numero."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Complemento:</b></td>";
        $modalBody .= "<td>".$company->complemento."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "</thead>";
        $modalBody .= "</table>";
        $modalBody .= "</div>";
        $modalBody .= "</div>";

        return response()->json($modalBody);
    }

    public function getBrasilianBanks(){

        $banks = array(
            array('codigo' => '001', 'nome' => 'Banco do Brasil'),
            array('codigo' => '003', 'nome' => 'Banco da Amazônia'),
            array('codigo' => '004', 'nome' => 'Banco do Nordeste'),
            array('codigo' => '021', 'nome' => 'Banestes'),
            array('codigo' => '025', 'nome' => 'Banco Alfa'),
            array('codigo' => '027', 'nome' => 'Besc'),
            array('codigo' => '029', 'nome' => 'Banerj'),
            array('codigo' => '031', 'nome' => 'Banco Beg'),
            array('codigo' => '033', 'nome' => 'Banco Santander Banespa'),
            array('codigo' => '036', 'nome' => 'Banco Bem'),
            array('codigo' => '037', 'nome' => 'Banpará'),
            array('codigo' => '038', 'nome' => 'Banestado'),
            array('codigo' => '039', 'nome' => 'BEP'),
            array('codigo' => '040', 'nome' => 'Banco Cargill'),
            array('codigo' => '041', 'nome' => 'Banrisul'),
            array('codigo' => '044', 'nome' => 'BVA'),
            array('codigo' => '045', 'nome' => 'Banco Opportunity'),
            array('codigo' => '047', 'nome' => 'Banese'),
            array('codigo' => '062', 'nome' => 'Hipercard'),
            array('codigo' => '063', 'nome' => 'Ibibank'),
            array('codigo' => '065', 'nome' => 'Lemon Bank'),
            array('codigo' => '066', 'nome' => 'Banco Morgan Stanley Dean Witter'),
            array('codigo' => '069', 'nome' => 'BPN Brasil'),
            array('codigo' => '070', 'nome' => 'Banco de Brasília – BRB'),
            array('codigo' => '072', 'nome' => 'Banco Rural'),
            array('codigo' => '073', 'nome' => 'Banco Popular'),
            array('codigo' => '074', 'nome' => 'Banco J. Safra'),
            array('codigo' => '075', 'nome' => 'Banco CR2'),
            array('codigo' => '076', 'nome' => 'Banco KDB'),
            array('codigo' => '096', 'nome' => 'Banco BMF'),
            array('codigo' => '104', 'nome' => 'Caixa Econômica Federal'),
            array('codigo' => '107', 'nome' => 'Banco BBM'),
            array('codigo' => '116', 'nome' => 'Banco Único'),
            array('codigo' => '151', 'nome' => 'Nossa Caixa'),
            array('codigo' => '175', 'nome' => 'Banco Finasa'),
            array('codigo' => '184', 'nome' => 'Banco Itaú BBA'),
            array('codigo' => '204', 'nome' => 'American Express Bank'),
            array('codigo' => '208', 'nome' => 'Banco Pactual'),
            array('codigo' => '212', 'nome' => 'Banco Matone'),
            array('codigo' => '213', 'nome' => 'Banco Arbi'),
            array('codigo' => '214', 'nome' => 'Banco Dibens'),
            array('codigo' => '217', 'nome' => 'Banco Joh Deere'),
            array('codigo' => '218', 'nome' => 'Banco Bonsucesso'),
            array('codigo' => '222', 'nome' => 'Banco Calyon Brasil'),
            array('codigo' => '224', 'nome' => 'Banco Fibra'),
            array('codigo' => '225', 'nome' => 'Banco Brascan'),
            array('codigo' => '229', 'nome' => 'Banco Cruzeiro'),
            array('codigo' => '230', 'nome' => 'Unicard'),
            array('codigo' => '233', 'nome' => 'Banco GE Capital'),
            array('codigo' => '237', 'nome' => 'Bradesco'),
            array('codigo' => '241', 'nome' => 'Banco Clássico'),
            array('codigo' => '243', 'nome' => 'Banco Stock Máxima'),
            array('codigo' => '246', 'nome' => 'Banco ABC Brasil'),
            array('codigo' => '248', 'nome' => 'Banco Boavista Interatlântico'),
            array('codigo' => '249', 'nome' => 'Investcred Unibanco'),
            array('codigo' => '250', 'nome' => 'Banco Schahin'),
            array('codigo' => '252', 'nome' => 'Fininvest'),
            array('codigo' => '254', 'nome' => 'Paraná Banco'),
            array('codigo' => '263', 'nome' => 'Banco Cacique'),
            array('codigo' => '265', 'nome' => 'Banco Fator'),
            array('codigo' => '266', 'nome' => 'Banco Cédula'),
            array('codigo' => '300', 'nome' => 'Banco de la Nación Argentina'),
            array('codigo' => '318', 'nome' => 'Banco BMG'),
            array('codigo' => '320', 'nome' => 'Banco Industrial e Comercial'),
            array('codigo' => '356', 'nome' => 'ABN Amro Real'),
            array('codigo' => '341', 'nome' => 'Itau'),
            array('codigo' => '347', 'nome' => 'Sudameris'),
            array('codigo' => '351', 'nome' => 'Banco Santander'),
            array('codigo' => '353', 'nome' => 'Banco Santander Brasil'),
            array('codigo' => '366', 'nome' => 'Banco Societe Generale Brasil'),
            array('codigo' => '370', 'nome' => 'Banco WestLB'),
            array('codigo' => '376', 'nome' => 'JP Morgan'),
            array('codigo' => '389', 'nome' => 'Banco Mercantil do Brasil'),
            array('codigo' => '394', 'nome' => 'Banco Mercantil de Crédito'),
            array('codigo' => '399', 'nome' => 'HSBC'),
            array('codigo' => '409', 'nome' => 'Unibanco'),
            array('codigo' => '412', 'nome' => 'Banco Capital'),
            array('codigo' => '422', 'nome' => 'Banco Safra'),
            array('codigo' => '453', 'nome' => 'Banco Rural'),
            array('codigo' => '456', 'nome' => 'Banco Tokyo Mitsubishi UFJ'),
            array('codigo' => '464', 'nome' => 'Banco Sumitomo Mitsui Brasileiro'),
            array('codigo' => '477', 'nome' => 'Citibank'),
            array('codigo' => '479', 'nome' => 'Itaubank (antigo Bank Boston)'),
            array('codigo' => '487', 'nome' => 'Deutsche Bank'),
            array('codigo' => '488', 'nome' => 'Banco Morgan Guaranty'),
            array('codigo' => '492', 'nome' => 'Banco NMB Postbank'),
            array('codigo' => '494', 'nome' => 'Banco la República Oriental del Uruguay'),
            array('codigo' => '495', 'nome' => 'Banco La Provincia de Buenos Aires'),
            array('codigo' => '505', 'nome' => 'Banco Credit Suisse'),
            array('codigo' => '600', 'nome' => 'Banco Luso Brasileiro'),
            array('codigo' => '604', 'nome' => 'Banco Industrial'),
            array('codigo' => '610', 'nome' => 'Banco VR'),
            array('codigo' => '611', 'nome' => 'Banco Paulista'),
            array('codigo' => '612', 'nome' => 'Banco Guanabara'),
            array('codigo' => '613', 'nome' => 'Banco Pecunia'),
            array('codigo' => '623', 'nome' => 'Banco Panamericano'),
            array('codigo' => '626', 'nome' => 'Banco Ficsa'),
            array('codigo' => '630', 'nome' => 'Banco Intercap'),
            array('codigo' => '633', 'nome' => 'Banco Rendimento'),
            array('codigo' => '634', 'nome' => 'Banco Triângulo'),
            array('codigo' => '637', 'nome' => 'Banco Sofisa'),
            array('codigo' => '638', 'nome' => 'Banco Prosper'),
            array('codigo' => '643', 'nome' => 'Banco Pine'),
            array('codigo' => '652', 'nome' => 'Itaú Holding Financeira'),
            array('codigo' => '653', 'nome' => 'Banco Indusval'),
            array('codigo' => '654', 'nome' => 'Banco A.J. Renner'),
            array('codigo' => '655', 'nome' => 'Banco Votorantim'),
            array('codigo' => '707', 'nome' => 'Banco Daycoval'),
            array('codigo' => '719', 'nome' => 'Banif'),
            array('codigo' => '721', 'nome' => 'Banco Credibel'),
            array('codigo' => '734', 'nome' => 'Banco Gerdau'),
            array('codigo' => '735', 'nome' => 'Banco Pottencial'),
            array('codigo' => '738', 'nome' => 'Banco Morada'),
            array('codigo' => '739', 'nome' => 'Banco Galvão de Negócios'),
            array('codigo' => '740', 'nome' => 'Banco Barclays'),
            array('codigo' => '741', 'nome' => 'BRP'),
            array('codigo' => '743', 'nome' => 'Banco Semear'),
            array('codigo' => '745', 'nome' => 'Banco Citibank'),
            array('codigo' => '746', 'nome' => 'Banco Modal'),
            array('codigo' => '747', 'nome' => 'Banco Rabobank International'),
            array('codigo' => '748', 'nome' => 'Banco Cooperativo Sicredi'),
            array('codigo' => '749', 'nome' => 'Banco Simples'),
            array('codigo' => '751', 'nome' => 'Dresdner Bank'),
            array('codigo' => '752', 'nome' => 'BNP Paribas'),
            array('codigo' => '753', 'nome' => 'Banco Comercial Uruguai'),
            array('codigo' => '755', 'nome' => 'Banco Merrill Lynch'),
            array('codigo' => '756', 'nome' => 'Banco Cooperativo do Brasil'),
            array('codigo' => '757', 'nome' => 'KEB'),
        );

        return $banks;

    }

}


