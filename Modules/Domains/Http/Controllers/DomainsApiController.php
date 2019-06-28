<?php

namespace Modules\Dominios\Http\Controllers;

use Exception;
use App\Dominio;
use App\Projeto;
use App\Entities\UserProjeto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\User;
use Cloudflare\API\Endpoints\Zones;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Dominios\Transformers\DominiosResource;

class domainsApiController extends Controller {

    public function index(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $dominios = Dominio::select('id','dominio','ip_dominio','status')
                            ->where('projeto',$projeto['id']);

        return DominiosResource::collection($dominios->paginate());
    }

    public function store(Request $request) {

        $dados = $request->all();
        $projeto = Projeto::find(Hashids::decode($request->projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $dados['projeto'] = $projeto['id'];

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

    public function show(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $dominio = Dominio::find(Hashids::decode($request->id_dominio));

        if(!$dominio){
            return response()->json('domínio não encontrado');
        }

        $key = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');

        $adapter = new Guzzle($key);

        $user = new User($adapter);

        $dns = new DNS($adapter);
 
        $zones = new Zones($adapter);

        $name_servers = [];
        foreach($zones->listZones()->result as $zone){
            if($zone->name == $dominio['dominio']){

                foreach($zone->name_servers as $new_name_server){
                    $name_servers[] = $new_name_server;
                }
            }
        }

        return response()->json([
            'dominio' => $dominio['dominio'],
            'ip_dominio' => $dominio['ip_dominio'],
            'servidores_dns' => $name_servers
        ]);
    }

    public function update(Request $request) {

    }

    public function destroy() {

        $dados = $request->all();

        $projeto = Projeto::find(Hashids::decode($request->projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $dominio = Dominio::find(Hashids::decode($dados['id']));

        if(!$dominio){
            return response()->json('domínio não encontrado');
        }

        $key = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');

        $adapter = new Guzzle($key);

        $zones = new Zones($adapter); 

        try{
            $zones->deleteZone($zones->getZoneID($dominio['dominio']));
        }
        catch(Exception $e){
            dd($e);
            return response()->json('Não foi possível deletar o domínio!');
        }

        Dominio::find($dados['id'])->delete();

        return response()->json('sucesso');

    }

    public function storeDns(Request $request){

        $dados = $request->all();

        $projeto = Projeto::find(Hashids::decode($request->projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $dominio = Dominio::find(Hashids::decode($request->id_dominio));

        if(!$dominio){
            return response()->json('domínio não encontrado');
        }

        $key = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns = new DNS($adapter);
        $zones = new Zones($adapter);
        $zoneID = $zones->getZoneID($dominio['dominio']);

        try{
            $dns->addRecord($zoneID, $dados['tipo_registro'], $dados['nome_registro'], $dados['valor_registro'], 0, true);
        }
        catch(Exception $e){
            return response()->json('Não foi possível adicionar o novo registro DNS, verifique os dados informados !');
        }

        return response()->json('sucesso');
    }

    public function destroyDns(Request $request){

        $dados = $request->all();

        $projeto = Projeto::find(Hashids::decode($request->projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $dominio = Dominio::find(Hashids::decode($request->id_dominio));

        if(!$dominio){
            return response()->json('domínio não encontrado');
        }

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

    public function isAuthorized($id_projeto){

        $projeto_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor'],
            ['projeto', $id_projeto]
        ])->first();

        if(!$projeto_usuario){
            return false;
        }

        return true;
    }
}
