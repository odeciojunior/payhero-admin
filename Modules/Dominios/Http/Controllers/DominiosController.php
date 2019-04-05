<?php

namespace Modules\Dominios\Http\Controllers;

use Exception;
use App\Layout;
use App\Dominio;
use App\Empresa;
use App\Projeto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\User;
use Illuminate\Routing\Controller;
use Cloudflare\API\Endpoints\Zones;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\AutorizacaoHelper;


class DominiosController extends Controller {

    public function cadastrarDominio(Request $request){

        $dados = $request->all();

        $dados['projeto'] = Hashids::decode($dados['projeto']);

        $projeto = Projeto::find($dados['projeto']);

        $key = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns = new DNS($adapter);
        $zones = new Zones($adapter);

        try{
            $zones->addZone($dados['dominio']);
        }
        catch(Exception $e){
            return response()->json('Não foi possível adicionar o domínio, verifique os dados informados!');
        }
 
        $zoneID = $zones->getZoneID($dados['dominio']);

        try{
            if($projeto['shopify_id'] == ''){
                if ($dns->addRecord($zoneID, "A", $dados['dominio'], $dados['ip_dominio'], 0, true) === true) {
                    // echo "DNS criado.". PHP_EOL;
                }
                if ($dns->addRecord($zoneID, "CNAME", 'www', $dados['dominio'], 0, true) === true) {
                    // echo "DNS criado.". PHP_EOL;
                }
                if ($dns->addRecord($zoneID, "A", 'checkout', '104.248.122.89', 0, true) === true) {
                    // echo "DNS criado.". PHP_EOL;
                }
                if ($dns->addRecord($zoneID, "A", 'sac', '104.248.122.89', 0, true) === true) {
                    // echo "DNS criado.". PHP_EOL;
                }
                if ($dns->addRecord($zoneID, "A", 'affiliate', '104.248.122.89', 0, true) === true) {
                    // echo "DNS criado.". PHP_EOL;
                }
            }
            else{
                $dados['ip_dominio'] = 'Domínio Shopify';
                if ($dns->addRecord($zoneID, "A", $dados['dominio'],'23.227.38.32', 0, true) === true) {
                    // echo "DNS criado.". PHP_EOL;
                }
                if ($dns->addRecord($zoneID, "CNAME", 'www', 'shops.myshopify.com', 0, true) === true) {
                    // echo "DNS criado.". PHP_EOL;
                }
                if ($dns->addRecord($zoneID, "A", 'checkout', '104.248.122.89', 0, true) === true) {
                    // echo "DNS criado.". PHP_EOL;
                }
                if ($dns->addRecord($zoneID, "A", 'sac', '104.248.122.89', 0, true) === true) {
                    // echo "DNS criado.". PHP_EOL;
                }
                if ($dns->addRecord($zoneID, "A", 'affiliate', '104.248.122.89', 0, true) === true) {
                    // echo "DNS criado.". PHP_EOL;
                }
            }
        }
        catch(Exception $e){
            try{
                $zones->deleteZone($zoneID); 
            }
            catch(Exception $e){
                //
            }
            return response()->json('Não foi possível adicionar o domínio, verifique os dados informados !');
        }

        $dados['status'] = "Conectado";
        Dominio::create($dados);

        return response()->json('sucesso');
    }

    public function editarDominio($id){

        $dominio = Dominio::find($id);
        $layouts = Layout::all();
        $empresas = Empresa::all();

        return view('dominios::editar',[
            'dominio' => $dominio,
            'layouts' => $layouts,
            'empresas' => $empresas
        ]);

    }

    public function updateDominio(Request $request){

        $dados = $request->all();

        $dominio = Dominio::find($dados['id']);

        $key = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns = new DNS($adapter);
        $zones = new Zones($adapter);
        $zoneID = $zones->getZoneID($dominio['dominio']);

        for($x = 1; $x < 10; $x++){
 
            if(isset($dados['tipo_registro_'.$x])){
 
                try{
                    $dns->addRecord($zoneID, $dados['tipo_registro_'.$x], $dados['nome_registro_'.$x], $dados['valor_registro_'.$x], 0, true);
                }
                catch(Exception $e){
                    return response()->json('Não foi possível adicionar o novo registro DNS, verifique os dados informados !');
                }

            }
        }

        return response()->json('sucesso');
    }

    public function deletarDominio(Request $request) {

        $dados = $request->all();

        $dominio = Dominio::where('id',Hashids::decode($dados['id']))->first();

        $key = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');

        $adapter = new Guzzle($key);

        $zones = new Zones($adapter); 

        try{
            $zones->deleteZone($zones->getZoneID($dominio['dominio']));
        }
        catch(Exception $e){
            dd($e);
            flash('Não foi possível deletar o domínio!')->error();
            return response()->json('Não foi possível deletar o domínio!');
        }

        Dominio::find($dados['id'])->delete();

        return response()->json('sucesso');

    }

    public function dadosDominios(Request $request) {

        $dados = $request->all();
    
        $dominios = \DB::table('dominios as dominio');

        if(isset($dados['projeto'])){
            $dominios = $dominios->where('dominio.projeto','=', Hashids::decode($dados['projeto']));
        }
        else{
            return response()->json('projeto não informado');
        }

        $dominios = $dominios->get([
                'dominio.id',
                'dominio.dominio',
                'dominio.ip_dominio',
                'dominio.status'
        ]);

        $key = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');

        $adapter = new Guzzle($key);
        $zones = new Zones($adapter);

        return Datatables::of($dominios)
            ->addColumn('status', function ($dominio) use($zones) {
                try{
                    $zoneID = $zones->getZoneID($dominio->dominio); 
                    $status = $zones->activationCheck($zoneID);
                    if($status){
                        Dominio::find($dominio->id)->update(['status' => 'Conectado']);
                        return "<span class='badge badge-success'>Conectado</span>";
                    }
                    else{
                        Dominio::find($dominio->id)->update(['status' => 'Desconectado']);
                        return "<span class='badge badge-warning'>Desconectado</span>";
                    }
                    return $status;
                }
                catch(\Exception $e){
                    if($dominio->status == 'Conectado')
                        return "<span class='badge badge-success'>".$dominio->status."</span>";
                    else
                        return "<span class='badge badge-warning'>".$dominio->status."</span>";
                }
            })
            ->addColumn('detalhes', function ($dominio) {
                return "<span data-toggle='modal' data-target='#modal_detalhes'>
                            <a class='btn btn-outline btn-success detalhes_dominio' data-placement='top' data-toggle='tooltip' title='Detalhes' dominio='".Hashids::encode($dominio->id)."'>
                                <i class='icon wb-order' aria-hidden='true'></i>
                            </a>
                        </span>
                        <span data-toggle='modal' data-target='#modal_editar'>
                            <a class='btn btn-outline btn-primary editar_dominio' data-placement='top' data-toggle='tooltip' title='Editar' dominio='".Hashids::encode($dominio->id)."'>
                                <i class='icon wb-pencil' aria-hidden='true'></i>
                            </a>
                        </span>
                        <span data-toggle='modal' data-target='#modal_excluir'>
                            <a class='btn btn-outline btn-danger excluir_dominio' data-placement='top' data-toggle='tooltip' title='Excluir' dominio='".Hashids::encode($dominio->id)."'>
                                <i class='icon wb-trash' aria-hidden='true'></i>
                            </a>
                        </span>";
        })
        ->rawColumns(['detalhes','status'])
        ->make(true);
    }

    public function detalhesDominio(Request $request){

        $dados = $request->all();

        $dominio = Dominio::where('id',Hashids::decode($dados['dominio']))->first();

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Domínio:</b></td>";
        $modal_body .= "<td>".$dominio['dominio']."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>IP que o domínio aponta:</b></td>";
        $modal_body .= "<td>".$dominio['ip_dominio']."</td>";
        $modal_body .= "</tr>";

        $key = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');

        $adapter = new Guzzle($key);

        $user = new User($adapter);

        $dns = new DNS($adapter);
 
        $zones = new Zones($adapter);

        foreach($zones->listZones()->result as $zone){
            if($zone->name == $dominio['dominio']){

                $x = 1;
                foreach($zone->name_servers as $new_name_server){
                    $modal_body .= "<tr>";
                    $modal_body .= "<td><b>Novo servidor DNS ". $x++ .":</b></td>";
                    $modal_body .= "<td>".$new_name_server."</td>";
                    $modal_body .= "</tr>";
                }

            }
        }

        return response()->json($modal_body);

    }

    public function getFormAddDominio(Request $request){

        $dados = $request->all();

        if(!isset($dados['projeto'])){
            return response()->json('Erro, projeto não encontrado');
        }        

        $projeto = Projeto::find($dados['projeto']);

        $form = view('dominios::cadastro',[
            'projeto' => $projeto,
        ]);

        return response()->json($form->render());
    }

    public function getFormEditarDominio(Request $request){

        $dados = $request->all();

        $dominio = Dominio::where('id',Hashids::decode($dados['id']))->first();

        $projeto = Projeto::find($dominio['projeto']);

        $key = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns = new DNS($adapter);
        $zones = new Zones($adapter);

        try{
            $zoneID = $zones->getZoneID($dominio['dominio']);
        }
        catch(\Exception $e){
            $form = view('dominios::editar',[
                'dominio' => $dominio,
            ]);
    
            return response()->json($form->render());   
        }

        $registros = [];

        foreach($dns->listRecords($zoneID)->result as $record){

            $novo_registro['id'] = $record->id;
            $novo_registro['tipo'] = $record->type; 

            if($record->name == $dominio['dominio']){
                $novo_registro['nome'] = $record->name;
            }
            else{
                $subdominio = explode('.',$record->name);
                $novo_registro['nome'] = $subdominio[0];
            }
            if($record->content == "104.248.122.89")
                $novo_registro['valor'] = "Servidores CloudFox";
            else
                $novo_registro['valor'] = $record->content;

            if($novo_registro['nome'] == 'checkout' || $novo_registro['nome'] == 'sac' || $novo_registro['nome'] == 'affiliate' || $novo_registro['nome'] == 'www' || $novo_registro['nome'] == $dominio['dominio']){
                $novo_registro['deletar'] = false; 
            }
            else{
                $novo_registro['deletar'] = true;
            }

            $registros[] = $novo_registro;
        }

        $form = view('dominios::editar',[
            'dominio' => $dominio,
            'registros' => $registros,
            'projeto' => $projeto
        ]);

        return response()->json($form->render());
    }

    public function removerRegistroDns(Request $request){

        $dados = $request->all();

        $dominio = Dominio::find($dados['id_dominio']);

        $key = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns = new DNS($adapter);
        $zones = new Zones($adapter);
        $zoneID = $zones->getZoneID($dominio['dominio']);

        try{
            $dns->deleteRecord($zoneID,$dados['id_registro']);
        }
        catch(\Exception $e){
            return response()->json('Ocorreu algum erro ao remover o registro DNS');
        }

        return response()->json('sucesso');
    }

}
