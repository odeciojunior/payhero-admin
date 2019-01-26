<?php

namespace Modules\Empresas\Http\Controllers;

use Auth;
use App\User;
use App\Empresa;
use PagarMe\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class EmpresasController extends Controller {

    public function index() {

        return view('empresas::index'); 
    }

    public function cadastro() {

        $bancos = $this->getBancosBrasileiros();

        return view('empresas::cadastro',[
            'bancos' => $bancos
        ]);
    }

    public function cadastrarEmpresa(Request $request){

        $dados = $request->all();

        $dados['user'] = \Auth::user()->id;

        $empresa = Empresa::create($dados);

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        try {
            $bankAccount = $pagarMe->bankAccounts()->create([
                'bank_code' => $dados['banco'],
                'agencia' => $dados['agencia'],
                'agencia_dv' => $dados['agencia_digito'],
                'conta' => $dados['conta'],
                'conta_dv' => $dados['conta_digito'],
                'document_number' => $dados['cnpj'],
                'legal_name' => $dados['nome_fantasia']
            ]);
        }
        catch(\Exception $e){
            return redirect()->route('empresas.editar',[
                'id' => $empresa->id
            ])->with('error', 'Empresa cadastrada, porém os dados bancários informados são inválidos');
        }

        $recipient = $pagarMe->recipients()->create([
            'anticipatable_volume_percentage' => '80',
            'automatic_anticipation_enabled' => 'false',
            'bank_account_id' => $bankAccount->id,
            'transfer_enabled' => 'true',
        ]);

        $empresa->update([
            'bank_account_id' => $bankAccount->id,
            'recipient_id'    => $recipient->id
        ]);

        return redirect()->route('empresas');
    }

    public function editarEmpresa($id){

        $empresa = Empresa::find($id);

        $bancos = $this->getBancosBrasileiros();

        return view('empresas::editar',[
            'empresa' => $empresa,
            'bancos'  => $bancos
        ]);

    }

    public function updateEmpresa(Request $request){

        $dados = $request->all();

        $empresa = Empresa::find($dados['id']);
        $empresa->update($dados);

        if($empresa->recipient_id == null){

            if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
                $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
            }
            else{
                $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
            }

            try {
                $bankAccount = $pagarMe->bankAccounts()->create([
                    'bank_code' => $dados['banco'],
                    'agencia' => $dados['agencia'],
                    'agencia_dv' => $dados['agencia_digito'],
                    'conta' => $dados['conta'],
                    'conta_dv' => $dados['conta_digito'],
                    'document_number' => $dados['cnpj'],
                    'legal_name' => $dados['nome_fantasia']
                ]);
            }
            catch(\Exception $e){

                return redirect()->route('empresas.editar',[
                    'id' => $empresa->id
                ])->with('error', 'Dados bancários informados inválidos');
            }

            $recipient = $pagarMe->recipients()->create([
                'anticipatable_volume_percentage' => '80',
                'automatic_anticipation_enabled' => 'false',
                'bank_account_id' => $bankAccount->id,
                'transfer_enabled' => 'true',
            ]);
    
            $empresa->update([
                'bank_account_id' => $bankAccount->id,
                'recipient_id'    => $recipient->id
            ]);
        }

        return redirect()->route('empresas');
    }

    public function deletarEmpresa($id){

        $empresa = Empresa::find($id)->delete();

        return redirect()->route('empresas');

    }

    public function dadosEmpresas() {

        $empresas = \DB::table('empresas as empresa')
            ->select([
                'empresa.id',
                'empresa.cnpj',
                'empresa.nome_fantasia',
        ]); 

        if(!\Auth::user()->hasRole('administrador geral')){
            $empresas = $empresas->where('user',\Auth::user()->id);
        }

        return Datatables::of($empresas)
        ->editColumn('cnpj', function ($empresa) {
            if(strlen($empresa->cnpj) == '14')
                return vsprintf("%s%s.%s%s%s.%s%s%s/%s%s%s%s-%s%s", str_split($empresa->cnpj));
            elseif(strlen($empresa->cnpj) == '11')
                return vsprintf("%s%s%s.%s%s%s.%s%s%s-%s%s", str_split($empresa->cnpj));
            return $empresa->cnpj;
        })
        ->addColumn('detalhes', function ($empresa) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_empresa' data-placement='top' data-toggle='tooltip' title='Detalhes' empresa='".$empresa->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/empresas/editar/$empresa->id' class='btn btn-outline btn-primary editar_empresa' data-placement='top' data-toggle='tooltip' title='Editar' empresa='".$empresa->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_empresa' data-placement='top' data-toggle='tooltip' title='Excluir' empresa='".$empresa->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesEmpresa(Request $request){

        $dados = $request->all();

        $empresa = Empresa::find($dados['id_empresa']);

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Status da conta bancária:</b></td>";
        if($empresa->recipient_id != '')
            $modal_body .= "<td>Ativa</td>";
        else
            $modal_body .= "<td>Inativa</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>CNPJ:</b></td>";
        $modal_body .= "<td>".$empresa->cnpj."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Nome fantasia:</b></td>";
        $modal_body .= "<td>".$empresa->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Email:</b></td>";
        $modal_body .= "<td>".$empresa->email."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Telefone:</b></td>";
        $modal_body .= "<td>".$empresa->telefone."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>CEP:</b></td>";
        $modal_body .= "<td>".$empresa->cep."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Estado:</b></td>";
        $modal_body .= "<td>".$empresa->estado."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Cidade:</b></td>";
        $modal_body .= "<td>".$empresa->cidade."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Bairro:</b></td>";
        $modal_body .= "<td>".$empresa->bairro."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Rua:</b></td>";
        $modal_body .= "<td>".$empresa->logradouro."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Número:</b></td>";
        $modal_body .= "<td>".$empresa->numero."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Complemento:</b></td>";
        $modal_body .= "<td>".$empresa->complemento."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "</div>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
    }

    public function getBancosBrasileiros(){

        $bancos = array(
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

        return $bancos;

    }

}


