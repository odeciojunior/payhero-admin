<?php

namespace Modules\Planos\Http\Controllers;

use App\Foto;
use App\Plano;
use App\Transportadora;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class PlanosController extends Controller {

    public function index() {

        return view('planos::index'); 
    }

    public function cadastro() {

        $transportadoras = Transportadora::all();

        return view('planos::cadastro',[
            'transportadoras' => $transportadoras
        ]);
    }

    public function cadastrarPlano(Request $request){

        $dados = $request->all();

        $novo_codigo_identificador = false;

        while($novo_codigo_identificador == false){

            $codigo_identificador = $this->randString(3).rand(100,999);
            $plano = Plano::where('cod_identificador', $codigo_identificador)->first();
            if($plano == null){
                $novo_codigo_identificador = true;
                $dados['cod_identificador'] = $codigo_identificador;
            }
        }

        $plano = Plano::create($dados);

        $foto = $request->file('foto');

        if ($foto != null) {
            $nome_foto = 'plano_' . $plano->id . '_.' . $foto->getClientOriginalExtension();

            $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO, $nome_foto);

            Foto::create([
                'caminho_imagem' => $nome_foto,
                'plano' => $plano['id'],
            ]);

        }

        return redirect()->route('planos');
    }

    public function editarPlano($id){

        $plano = Plano::find($id);
        $transportadoras = Transportadora::all();
        $foto = Foto::where('plano',$plano['id'])->first();

        if($foto != null){
            $caminho_foto = url(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO.$foto->caminho_imagem);
        }
        else{
            $caminho_foto = null;
        }

        return view('planos::editar',[
            'plano' => $plano,
            'transportadoras' => $transportadoras,
            'foto' => $caminho_foto,
        ]);

    }

    public function updatePlano(Request $request){

        $dados = $request->all();

        $plano = Plano::find($dados['id']);
        $plano->update($dados);

        $foto = $request->file('foto');

        if ($foto != null) {
            $nome_foto = 'plano_' . $plano->id . '_.' . $foto->getClientOriginalExtension();

            $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO, $nome_foto);

        }

        return redirect()->route('planos');
    }

    public function deletarPlano($id){

        Plano::find($id)->delete();

        return redirect()->route('planos');

    }

    public function dadosPlano() {

        $planos = \DB::table('planos as plano')
            ->get([
                'plano.id',
                'plano.nome',
                'plano.descricao',
                'plano.cod_identificador',
                'plano.preco',
        ]);

        return Datatables::of($planos)
        ->addColumn('detalhes', function ($plano) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_plano' data-placement='top' data-toggle='tooltip' title='Detalhes' plano='".$plano->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/planos/editar/$plano->id' class='btn btn-outline btn-primary editar_plano' data-placement='top' data-toggle='tooltip' title='Editar' plano='".$plano->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_plano' data-placement='top' data-toggle='tooltip' title='Excluir' plano='".$plano->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesPlano(Request $request){

        $dados = $request->all();

        $plano = Plano::find($dados['id_plano']);

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Nome:</b></td>";
        $modal_body .= "<td>".$plano->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Descrição:</b></td>";
        $modal_body .= "<td>".$plano->descricao."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Código identificador:</b></td>";
        $modal_body .= "<td>".$plano->cod_identificador."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Status:</b></td>";
        if($plano->status == 1)
            $modal_body .= "<td>Ativo</td>";
        else
            $modal_body .= "<td>Inativo</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Preço:</b></td>";
        $modal_body .= "<td>".$plano->preco."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Status cupons:</b></td>";
        if($plano->status_cupom == 1)
            $modal_body .= "<td>Cupons ativos</td>";
        else
            $modal_body .= "<td>Cupons não ativos</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Possui frete:</b></td>";
        if($plano->frete == 1)
            $modal_body .= "<td>Sim</td>";
        else
            $modal_body .= "<td>Não</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Frete fixo:</b></td>";
        if($plano->frete_fixo == 1)
            $modal_body .= "<td>Sim</td>";
        else
            $modal_body .= "<td>Não</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Valor frete fixo:</b></td>";
        $modal_body .= "<td>".$plano->valor_frete."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $foto = Foto::where('plano', $plano->id)->first();
        if($foto != null)
            $modal_body .= "<img src='".url(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO.$foto->caminho_imagem)."'>";
        else
            $modal_body .= "<img src=''>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
    }

    function randString($size){

        $basic = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $return= "";

        for($count= 0; $size > $count; $count++){

            $return.= $basic[rand(0, strlen($basic) - 1)];
        }

        return $return;
    }
    
}
