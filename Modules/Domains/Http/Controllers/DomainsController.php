<?php

namespace Modules\Domains\Http\Controllers;

use Exception;
use App\Entities\Domain;
use App\Entities\Project;
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


class DomainsController extends Controller {

    public function index(Request $request) {

        $requestData = $request->all();
    
        $domains = \DB::table('domains as domain');

        if(isset($requestData['projeto'])){
            $domains = $domains->where('domain.project','=', Hashids::decode($requestData['projeto']));
        }
        else{
            return response()->json('projeto não informado');
        }

        $domains = $domains->get([
                'domain.id',
                'domain.name',
                'domain.domain_ip',
                'domain.status'
        ]);

        $key = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');

        $adapter = new Guzzle($key);
        $zones   = new Zones($adapter);

        return Datatables::of($domains)
            ->addColumn('status', function ($domain) use($zones) {
                try{
                    $zoneID = $zones->getZoneID($domain->domain); 
                    $status = $zones->activationCheck($zoneID);
                    if($status){
                        Domain::find($domain->id)->update(['status' => 'Conectado']);
                        return "<span class='badge badge-success'>Conectado</span>";
                    }
                    else{
                        Domain::find($domain->id)->update(['status' => 'Desconectado']);
                        return "<span class='badge badge-warning'>Desconectado</span>";
                    }
                    return $status;
                }
                catch(\Exception $e){
                    if($domain->status == 'Conectado')
                        return "<span class='badge badge-success'>".$domain->status."</span>";
                    else
                        return "<span class='badge badge-warning'>".$domain->status."</span>";
                }
            })
            ->addColumn('detalhes', function ($domain) {
                return "<span data-toggle='modal' data-target='#modal_detalhes'>
                            <a class='btn btn-outline btn-success detalhes_domain' data-placement='top' data-toggle='tooltip' title='Detalhes' domain='".Hashids::encode($domain->id)."'>
                                <i class='icon wb-order' aria-hidden='true'></i>
                            </a>
                        </span>
                        <span data-toggle='modal' data-target='#modal_editar'>
                            <a class='btn btn-outline btn-primary editar_domain' data-placement='top' data-toggle='tooltip' title='Editar' domain='".Hashids::encode($domain->id)."'>
                                <i class='icon wb-pencil' aria-hidden='true'></i>
                            </a>
                        </span>
                        <span data-toggle='modal' data-target='#modal_excluir'>
                            <a class='btn btn-outline btn-danger excluir_domain' data-placement='top' data-toggle='tooltip' title='Excluir' domain='".Hashids::encode($domain->id)."'>
                                <i class='icon wb-trash' aria-hidden='true'></i>
                            </a>
                        </span>";
        })
        ->rawColumns(['detalhes','status'])
        ->make(true);
    }

    public function store(Request $request){

        $requestData = $request->all();

        $requestData['project'] = Hashids::decode($requestData['projeto'])[0];

        $project = Project::find($requestData['project']);

        $key     = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns     = new DNS($adapter);
        $zones   = new Zones($adapter);

        try{
            $zones->addZone($requestData['name']);
        }
        catch(Exception $e){
            dd($e);
            return response()->json('Não foi possível adicionar o domínio, verifique os dados informados!');
        }
 
        $zoneID = $zones->getZoneID($requestData['name']);

        try{
            if($project['shopify_id'] == ''){
                if ($dns->addRecord($zoneID, "A", $requestData['name'], $requestData['domain_ip'], 0, true) === true) {
                    // echo "DNS criado.". PHP_EOL;
                }
                if ($dns->addRecord($zoneID, "CNAME", 'www', $requestData['name'], 0, true) === true) {
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
                $requestData['domain_ip'] = 'Domínio Shopify';
                if ($dns->addRecord($zoneID, "A", $requestData['name'],'23.227.38.32', 0, true) === true) {
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
            dd($e);
            return response()->json('Não foi possível adicionar o domínio, verifique os dados informados !');
        }

        try{
            $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));

            $requestBody = json_decode('{
                "automatic_security": false,
                "custom_spf" : true,
                "default" : true,
                "domain" : "'.$requestData['name'].'"
            }');

            $response = $sendgrid->client->whitelabel()->domains()->post($requestBody);

            $response = json_decode($response->body());

            if(!isset($response->id)){
                dd($response);
            }

            $senderAuthenticationId = $response->id;

            $requestData['id_sendgrid'] = $response->id;

            $response = $sendgrid->client->whitelabel()->domains()->_($response->id)->get();

            $response = json_decode($response->body());

            foreach($response->dns as $responseDns){
                if($responseDns->type == 'mx'){
                    $dns->addRecord($zoneID, 'MX', $responseDns->host, $responseDns->data, 0, false,'1');
                }
                else{
                    $dns->addRecord($zoneID, strtoupper($responseDns->type), $responseDns->host, $responseDns->data, 0, false);
                }
            }

            $request_body = json_decode('{
                "default": true,
                "domain": "'.$requestData['name'].'",
                "subdomain": "mail"
            }');

            $query_params = json_decode('{"limit": 1, "offset": 1}');

            $response = $sendgrid->client->whitelabel()->links()->post($request_body, $query_params);

            $response = json_decode($response->body());

            $linkBrandingId = $response->id;

            foreach($response->dns as $responseDns){
                $dns->addRecord($zoneID, strtoupper($responseDns->type), $responseDns->host, $responseDns->data, 0, false);
            }

            sleep(5);
            $sendgrid->client->whitelabel()->domains()->_($senderAuthenticationId)->validate()->post();
            $response = $sendgrid->client->whitelabel()->links()->_($linkBrandingId)->validate()->post();
        }
        catch(Exception $e){
            dd($e);
        }

        $requestData['status'] = "Conectado";

        Domain::create($requestData);

        return response()->json('sucesso');
    }

    public function edit($id){

        $domain    = Domain::find($id);
        $companies = Company::all();

        return view('domains::editar',[
            'domain'    => $domain,
            'companies' => $companies
        ]);
    }

    public function update(Request $request){

        $requestData = $request->all();

        $domain = Domain::find($requestData['id']);

        $key     = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns     = new DNS($adapter);
        $zones   = new Zones($adapter);
        $zoneID  = $zones->getZoneID($domain['name']);

        for($x = 1; $x < 10; $x++){
 
            if(isset($requestData['tipo_registro_'.$x])){
 
                try{
                    $dns->addRecord($zoneID, $requestData['tipo_registro_'.$x], $requestData['nome_registro_'.$x], $requestData['valor_registro_'.$x], 0, true);
                }
                catch(Exception $e){
                    return response()->json('Não foi possível adicionar o novo registro DNS, verifique os dados informados !');
                }

            }
        }

        return response()->json('sucesso');
    }

    public function delete(Request $request) {

        $requestData = $request->all();

        $domain = Domain::where('id',Hashids::decode($requestData['id']))->first();

        $key     = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $zones   = new Zones($adapter); 

        try{
            $zones->deleteZone($zones->getZoneID($domain['name']));
        }
        catch(Exception $e){
            dd($e);
            flash('Não foi possível deletar o domínio!')->error();
            return response()->json('Não foi possível deletar o domínio!');
        }

        Domain::find($requestData['id'])->delete();

        return response()->json('sucesso');

    }

    public function details(Request $request){

        $requestData = $request->all();

        $domain = Domain::where('id',Hashids::decode($requestData['domain']))->first();

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Domínio:</b></td>";
        $modal_body .= "<td>".$domain['name']."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>IP que o domínio aponta:</b></td>";
        $modal_body .= "<td>".$domain['domain_ip']."</td>";
        $modal_body .= "</tr>";

        $key     = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $user    = new User($adapter);
        $dns     = new DNS($adapter); 
        $zones   = new Zones($adapter);

        foreach($zones->listZones()->result as $zone){
            if($zone->name == $domain['name']){

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

    public function create(Request $request){

        $requestData = $request->all();

        if(!isset($requestData['projeto'])){
            return response()->json('Erro, projeto não encontrado');
        }        

        $project = Project::find(Hashids::decode($requestData['projeto'])[0]);

        $form = view('domains::create',[
            'project' => $project
        ]);

        return response()->json($form->render());
    }

    public function getFormEditardomain(Request $request){

        $requestData = $request->all();

        $domain = Domain::where('id',Hashids::decode($requestData['id']))->first();

        $project = Project::find($domain['projeto']);

        $key     = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns     = new DNS($adapter);
        $zones   = new Zones($adapter);

        try{
            $zoneID = $zones->getZoneID($domain['name']);
        }
        catch(\Exception $e){
            $form = view('domains::editar',[
                'domain' => $domain,
            ]);
    
            return response()->json($form->render());   
        }

        $registros = [];

        foreach($dns->listRecords($zoneID)->result as $record){

            $novo_registro['id'] = $record->id;
            $novo_registro['tipo'] = $record->type; 

            if($record->name == $domain['name']){
                $novo_registro['nome'] = $record->name;
            }
            else{
                $subdomain = explode('.',$record->name);
                $novo_registro['nome'] = $subdomain[0];
            }
            if($record->content == "104.248.122.89")
                $novo_registro['valor'] = "Servidores CloudFox";
            else
                $novo_registro['valor'] = $record->content;

            if($novo_registro['nome'] == 'checkout' || $novo_registro['nome'] == 'sac' || $novo_registro['nome'] == 'affiliate' || $novo_registro['nome'] == 'www' || $novo_registro['nome'] == $domain['name']){
                $novo_registro['deletar'] = false; 
            }
            else{
                $novo_registro['deletar'] = true;
            }

            $registros[] = $novo_registro;
        }

        $form = view('domains::edit',[
            'domain'   => $domain,
            'registros' => $registros,
            'projeto'   => $project
        ]);

        return response()->json($form->render());
    }

    public function removerRegistroDns(Request $request){

        $requestData = $request->all();

        $domain = Domain::find($requestData['id_domain']);

        $key = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns = new DNS($adapter);
        $zones = new Zones($adapter);
        $zoneID = $zones->getZoneID($domain['name']);

        try{
            $dns->deleteRecord($zoneID,$requestData['id_registro']);
        }
        catch(\Exception $e){
            return response()->json('Ocorreu algum erro ao remover o registro DNS');
        }

        return response()->json('sucesso');
    }

}
