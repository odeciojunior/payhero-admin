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
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\AutorizacaoHelper;
use Modules\Dominios\Transformers\DomainResource;

class DomainsController extends Controller
{
    private $domainModel;
    private $projectModel;

    private function getDomain()
    {
        if (!$this->domainModel) {
            $this->domainModel = app(Domain::class);
        }

        return $this->domainModel; 
    }

    private function getProject()
    {
        if (!$this->projectModel) {
            $this->projectModel = app(Project::class);
        }

        return $this->projectModel; 
    }

    public function index(Request $request)
    {
        try {
            $projectId = $request->input("project");

            if ($projectId) {
                $projectId = Hashids::decode($projectId)[0];

                $project = $this->getProject()->with('domains')->find($projectId);

                return DomainResource::collection($project->domains);
            }
        }
        catch (Exception $e) {
            Log::warning('Erro ao buscar dados (DomainsController - index)');
            report($e);
        }
    }

    public function store(Request $request)
    {

        $requestData = $request->all();

        $requestData['project'] = Hashids::decode($requestData['projeto'])[0];

        $project = Project::find($requestData['project']);

        $key     = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns     = new DNS($adapter);
        $zones   = new Zones($adapter);

        try {
            $zones->addZone($requestData['name']);
        } catch (Exception $e) {
            dd($e);

            return response()->json('Não foi possível adicionar o domínio, verifique os dados informados!');
        }

        $zoneID = $zones->getZoneID($requestData['name']);

        try {
            if ($project['shopify_id'] == '') {
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
            } else {
                $requestData['domain_ip'] = 'Domínio Shopify';
                if ($dns->addRecord($zoneID, "A", $requestData['name'], '23.227.38.32', 0, true) === true) {
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
        } catch (Exception $e) {
            try {
                $zones->deleteZone($zoneID);
            } catch (Exception $e) {
                //
            }
            dd($e);

            return response()->json('Não foi possível adicionar o domínio, verifique os dados informados !');
        }

        try {
            $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));

            $requestBody = json_decode('{
                "automatic_security": false,
                "custom_spf" : true,
                "default" : true,
                "domain" : "' . $requestData['name'] . '"
            }');

            $response = $sendgrid->client->whitelabel()->domains()->post($requestBody);

            $response = json_decode($response->body());

            if (!isset($response->id)) {
                dd($response);
            }

            $senderAuthenticationId = $response->id;

            $requestData['id_sendgrid'] = $response->id;

            $response = $sendgrid->client->whitelabel()->domains()->_($response->id)->get();

            $response = json_decode($response->body());

            foreach ($response->dns as $responseDns) {
                if ($responseDns->type == 'mx') {
                    $dns->addRecord($zoneID, 'MX', $responseDns->host, $responseDns->data, 0, false, '1');
                } else {
                    $dns->addRecord($zoneID, strtoupper($responseDns->type), $responseDns->host, $responseDns->data, 0, false);
                }
            }

            $request_body = json_decode('{
                "default": true,
                "domain": "' . $requestData['name'] . '",
                "subdomain": "mail"
            }');

            $query_params = json_decode('{"limit": 1, "offset": 1}');

            $response = $sendgrid->client->whitelabel()->links()->post($request_body, $query_params);

            $response = json_decode($response->body());

            $linkBrandingId = $response->id;

            foreach ($response->dns as $responseDns) {
                $dns->addRecord($zoneID, strtoupper($responseDns->type), $responseDns->host, $responseDns->data, 0, false);
            }

            sleep(5);
            $sendgrid->client->whitelabel()->domains()->_($senderAuthenticationId)->validate()->post();
            $response = $sendgrid->client->whitelabel()->links()->_($linkBrandingId)->validate()->post();
        } catch (Exception $e) {
            dd($e);
        }

        $requestData['status'] = "Conectado";

        Domain::create($requestData);

        return response()->json('sucesso');
    }
 
    public function edit($id)
    {
        $domain    = Domain::find($id);
        $companies = Company::all();

        return view('domains::editar', [
            'domain'    => $domain,
            'companies' => $companies,
        ]);
    }

    public function update(Request $request)
    {

        $requestData = $request->all();

        $domain = Domain::find($requestData['id']);

        $key     = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns     = new DNS($adapter);
        $zones   = new Zones($adapter);
        $zoneID  = $zones->getZoneID($domain['name']);

        for ($x = 1; $x < 10; $x++) {

            if (isset($requestData['tipo_registro_' . $x])) {

                try {
                    $dns->addRecord($zoneID, $requestData['tipo_registro_' . $x], $requestData['nome_registro_' . $x], $requestData['valor_registro_' . $x], 0, true);
                } catch (Exception $e) {
                    return response()->json('Não foi possível adicionar o novo registro DNS, verifique os dados informados !');
                }
            }
        }

        return response()->json('sucesso');
    }

    public function delete(Request $request)
    {

        $requestData = $request->all();

        $domain = Domain::where('id', Hashids::decode($requestData['id']))->first();

        $key     = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $zones   = new Zones($adapter);

        try {
            $zones->deleteZone($zones->getZoneID($domain['name']));
        } catch (Exception $e) {
            dd($e);
            flash('Não foi possível deletar o domínio!')->error();

            return response()->json('Não foi possível deletar o domínio!');
        }

        Domain::find($requestData['id'])->delete();

        return response()->json('sucesso');
    }

    public function show($domainId)
    {
        $domain = $this->getDomain()->where('id', Hashids::decode($domainId))->first();

        $key     = new APIKey(getenv('CLOUDFLARE_EMAIL'), getenv('CLOUDFLARE_TOKEN'));
        $adapter = new Guzzle($key);
        $user    = new User($adapter);
        $dns     = new DNS($adapter);
        $zones   = new Zones($adapter);

        $view = view('domains::show',[
            'domain' => $domain,
            'zones'  => $zones->listZones()->result,
        ]);

        return response()->json($view->render());
    }

    public function create(Request $request)
    {
        try {

            $projectId = $request->project_id;

            if ($projectId) {
                $projectId = Hashids::decode($projectId)[0];
            }

            $project = $this->getProject()->find($projectId);

            return view('domains::create',[
                'project' => $project
            ]);
        }
        catch (Exception $e) {
            Log::warning('Erro ao obter form de cadastro de domínios (DomainsController - create)');
            report($e);
        }

        $requestData = $request->all();

        if (!isset($requestData['projeto'])) {
            return response()->json('Erro, projeto não encontrado');
        }

        $project = $this->getProject()->find(Hashids::decode($requestData['projeto'])[0]);

        $form = view('domains::create', [
            'project' => $project,
        ]);

        return response()->json($form->render());
    }

    public function getFormEditardomain(Request $request)
    {

        $requestData = $request->all();

        $domain = Domain::where('id', Hashids::decode($requestData['id']))->first();

        $project = Project::find($domain['projeto']);

        $key     = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns     = new DNS($adapter);
        $zones   = new Zones($adapter);

        try {
            $zoneID = $zones->getZoneID($domain['name']);
        } catch (\Exception $e) {
            $form = view('domains::editar', [
                'domain' => $domain,
            ]);

            return response()->json($form->render());
        }

        $registros = [];

        foreach ($dns->listRecords($zoneID)->result as $record) {

            $novo_registro['id']   = $record->id;
            $novo_registro['tipo'] = $record->type;

            if ($record->name == $domain['name']) {
                $novo_registro['nome'] = $record->name;
            } else {
                $subdomain             = explode('.', $record->name);
                $novo_registro['nome'] = $subdomain[0];
            }
            if ($record->content == "104.248.122.89")
                $novo_registro['valor'] = "Servidores CloudFox";
            else
                $novo_registro['valor'] = $record->content;

            if ($novo_registro['nome'] == 'checkout' || $novo_registro['nome'] == 'sac' || $novo_registro['nome'] == 'affiliate' || $novo_registro['nome'] == 'www' || $novo_registro['nome'] == $domain['name']) {
                $novo_registro['deletar'] = false;
            } else {
                $novo_registro['deletar'] = true;
            }

            $registros[] = $novo_registro;
        }

        $form = view('domains::edit', [
            'domain'    => $domain,
            'registros' => $registros,
            'projeto'   => $project,
        ]);

        return response()->json($form->render());
    }

    public function removerRegistroDns(Request $request)
    {

        $requestData = $request->all();

        $domain = Domain::find($requestData['id_domain']);

        $key     = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns     = new DNS($adapter);
        $zones   = new Zones($adapter);
        $zoneID  = $zones->getZoneID($domain['name']);

        try {
            $dns->deleteRecord($zoneID, $requestData['id_registro']);
        } catch (\Exception $e) {
            return response()->json('Ocorreu algum erro ao remover o registro DNS');
        }

        return response()->json('sucesso');
    }
}
