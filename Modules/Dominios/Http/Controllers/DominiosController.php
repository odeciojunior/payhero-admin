<?php

namespace Modules\Dominios\Http\Controllers;

use Exception;
use App\Layout;
use App\Dominio;
use App\Empresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\User;
use Illuminate\Routing\Controller;
use Cloudflare\API\Endpoints\Zones;
use Yajra\DataTables\Facades\DataTables;

class DominiosController extends Controller {

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index() {

        return view('dominios::index'); 
    }

    /**
     * Display a form to store new users.
     * @return Response
     */
    public function cadastro() {

        $layouts = Layout::all();
        $empresas = Empresa::all();

        return view('dominios::cadastro',[
            'layouts' => $layouts,
            'empresas' => $empresas
        ]);
    }

    public function cadastrarDominio(Request $request){

        $dados = $request->all();

        $key = new APIKey('adm@healthlab.io', 'cc871fd0763e1dbbb127a4c5a8bddbe24788a');

        $adapter = new Guzzle($key);

        $user = new User($adapter);

        $dns = new DNS($adapter);

        $zones = new Zones($adapter);

        try{
            $zones->addZone($dados['dominio']);
        }
        catch(Exception $e){
            flash('Não foi possível adicionar o domínio, domínio não registrado!')->error();
            return redirect()->route('dominios');
        }

        $zoneID = $zones->getZoneID($dados['dominio']);

        if ($dns->addRecord($zoneID, "A", $dados['dominio'], '104.248.122.89', 0, true) === true) {
            echo "DNS criado.". PHP_EOL;
        }
        if ($dns->addRecord($zoneID, "CNAME", 'www', $dados['dominio'], 0, true) === true) {
            echo "DNS criado.". PHP_EOL;
        }
        if ($dns->addRecord($zoneID, "A", 'checkout', '104.248.122.89', 0, true) === true) {
            echo "DNS criado.". PHP_EOL;
        }
        if ($dns->addRecord($zoneID, "A", 'sac', '104.248.122.89', 0, true) === true) {
            echo "DNS criado.". PHP_EOL;
        }

        Dominio::create($dados);

        return redirect()->route('dominios');
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

        Dominio::find($dados['id'])->update($dados);

        return redirect()->route('dominios');
    }

    public function deletarDominio($id) {

        $dominio = Dominio::find($id);

        $key = new APIKey('adm@healthlab.io', 'cc871fd0763e1dbbb127a4c5a8bddbe24788a');

        $adapter = new Guzzle($key);

        $zones = new Zones($adapter);

        try{
            $zones->deleteZone($zones->getZoneID($dominio['dominio']));
        }
        catch(Exception $e){
            flash('Não foi possível deletar o domínio!')->error();
            return redirect()->route('dominios');
        }

        Dominio::find($id)->delete();

        return redirect()->route('dominios');

    }

    /**
     * Return data for datatable
     */
    public function dadosDominios(Request $request) {

        $dados = $request->all();
    
        $dominios = \DB::table('dominios as dominio')
            ->leftJoin('layouts', 'dominio.layout', 'layouts.id')
            ->leftJoin('empresas', 'dominio.empresa', 'empresas.id');

        if(isset($dados['projeto'])){
            $dominios = $dominios->where('dominio.projeto','=', $dados['projeto']);
        }
            
        $dominios = $dominios->get([
                'dominio.id',
                'dominio.dominio',
                'dominio.layout',
                'dominio.empresa',
                'empresas.nome as empresa_nome',
                'layouts.descricao as layout_descricao',
        ]);

        return Datatables::of($dominios)
            ->addColumn('detalhes', function ($dominio) {
            return "<span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/dominios/editar/$dominio->id' class='btn btn-outline btn-primary editar_dominio' data-placement='top' data-toggle='tooltip' title='Editar' dominio='".$dominio->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_dominio' data-placement='top' data-toggle='tooltip' title='Excluir' dominio='".$dominio->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

}
