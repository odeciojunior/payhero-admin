<?php

namespace Modules\Transportadoras\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Transportadora;

class TransportadorasController extends Controller
{
    public function index() {

        return view('transportadoras::index'); 
    }

    public function cadastro() {

        return view('transportadoras::cadastro');
    }

    public function cadastrarTransportadora(Request $request){

        $dados = $request->all();

        Transportadora::create($dados);

        return redirect()->route('transportadoras');
    }

    public function editarTransportadora($id){

        $transportadora = transportadora::find($id);

        return view('transportadoras::editar',[
            'transportadora' => $transportadora,
        ]);

    }

    public function updateTransportadora(Request $request){

        $dados = $request->all();

        Transportadora::find($dados['id'])->update($dados);

        return view('transportadoras::index');
    }

    public function deletarTransportadora($id){

        Transportadora::find($id)->delete();

        return redirect()->route('transportadoras');

    }

    public function dadosTransportadora() {

        $transportadoras = \DB::table('transportadoras as transportadora')
            ->get([
                'id',
                'name',
                'site',
        ]);

        return Datatables::of($transportadoras)
        ->addColumn('detalhes', function ($transportadora) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_transportadora' data-placement='top' data-toggle='tooltip' title='Detalhes' transportadora='".$transportadora->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/transportadoras/editar/$transportadora->id' class='btn btn-outline btn-primary editar_transportadora' data-placement='top' data-toggle='tooltip' title='Editar' transportadora='".$transportadora->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_transportadora' data-placement='top' data-toggle='tooltip' title='Excluir' transportadora='".$transportadora->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesTransportadora(Request $request){

        $dados = $request->all();

        $transportadora = Transportadora::find($dados['id_transportadora']);

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Status:</b></td>";
        $modal_body .= "<td>".$transportadora->status."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>CNPJ:</b></td>";
        $modal_body .= "<td>".$transportadora->cnpj."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Nome fantasia:</b></td>";
        $modal_body .= "<td>".$transportadora->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Email:</b></td>";
        $modal_body .= "<td>".$transportadora->email."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Telefone:</b></td>";
        $modal_body .= "<td>".$transportadora->telefone."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>CEP:</b></td>";
        $modal_body .= "<td>".$transportadora->cep."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Estado:</b></td>";
        $modal_body .= "<td>".$transportadora->estado."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Cidade:</b></td>";
        $modal_body .= "<td>".$transportadora->cidade."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Bairro:</b></td>";
        $modal_body .= "<td>".$transportadora->bairro."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Rua:</b></td>";
        $modal_body .= "<td>".$transportadora->logradouro."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Número:</b></td>";
        $modal_body .= "<td>".$transportadora->numero."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Complemento:</b></td>";
        $modal_body .= "<td>".$transportadora->complemento."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Atividade principal:</b></td>";
        $modal_body .= "<td>".$transportadora->atividade_principal."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Capital social:</b></td>";
        $modal_body .= "<td>".$transportadora->capital_social."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Data de abertura:</b></td>";
        $modal_body .= "<td>".$transportadora->abertura."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "</div>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
    }
}


