<?php

namespace Modules\Shopify\Http\Controllers;

use App\Plano;
use Exception;
use App\Dominio;
use App\Empresa;
use App\Produto;
use App\Projeto;
use App\UserProjeto;
use App\ProdutoPlano;
use App\IntegracaoShopify;
use Slince\Shopify\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Adapter\Guzzle;
use Illuminate\Routing\Controller;
use Cloudflare\API\Endpoints\Zones;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class ShopifyController extends Controller {


    public function index() {

        $empresas = Empresa::where('user',\Auth::user()->id)->get()->toArray();

        $integracoes_shopify = IntegracaoShopify::where('user',\Auth::user()->id)->get()->toArray();

        $projetos = [];

        foreach($integracoes_shopify as $integracao_shopify){
            $projetos[] = Projeto::find($integracao_shopify['projeto']);
        }

        return view('shopify::index',[
            'empresas' => $empresas,
            'projetos' => $projetos,
        ]);
    }

    public function adicionarIntegracao(Request $request){

        $dados = $request->all();

        // try{
            $credential = new PublicAppCredential($dados['token']);
            //$credential = new PublicAppCredential('0fc0fea41cc38c989749dc9040794bb4');

            //$client = new Client($credential, 'canto-infantil.myshopify.com', [
            $client = new Client($credential, $dados['url_loja'], [
                'metaCacheDir' => './tmp' // Metadata cache dir, required
            ]);
        // }
        // catch(\Exception $e){
        //     return response()->json($e);
        //     return response()->json('Dados do shopify inválidos, revise os dados informados');
        // }

        $projeto = Projeto::create([
            'nome' => $client->getShopManager()->get()->getName(),
            'status' => '1',
            'visibilidade' => 'privado',
            'porcentagem_afiliados' => '0',
            'descricao' =>  $client->getShopManager()->get()->getName(),
            'descricao_fatura' => $client->getShopManager()->get()->getName(),
            'url_pagina' =>  'https://'.$client->getShopManager()->get()->getDomain(),
            'afiliacao_automatica' => false,
        ]);

        $key = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns = new DNS($adapter);
        $zones = new Zones($adapter);

        // try{
        //     $zones->addZone($client->getShopManager()->get()->getDomain());
        // }
        // catch(\Exception $e){
        //     $projeto->delete();
        //     return response()->json($e);
        //     return response()->json('Não foi possível adicionar o domínio, verifique os dados informados!');
        // }

        // $zoneID = $zones->getZoneID($client->getShopManager()->get()->getDomain());

        // try{
        //     if ($dns->addRecord($zoneID, "A", $client->getShopManager()->get()->getDomain(),'23.227.38.32', 0, true) === true) {
        //         // echo "DNS criado.". PHP_EOL;
        //     }
        //     if ($dns->addRecord($zoneID, "CNAME", 'www', 'shops.myshopify.com', 0, true) === true) {
        //         // echo "DNS criado.". PHP_EOL;
        //     }
        //     if ($dns->addRecord($zoneID, "A", 'checkout', '104.248.122.89', 0, true) === true) {
        //         // echo "DNS criado.". PHP_EOL;
        //     }
        //     if ($dns->addRecord($zoneID, "A", 'sac', '104.248.122.89', 0, true) === true) {
        //         // echo "DNS criado.". PHP_EOL;
        //     }
        // }

        // catch(Exception $e){
        //     try{
        //         $zones->deleteZone($zoneID); 
        //     }
        //     catch(Exception $e){
        //         //
        //     }
        //     $projeto->delete();
        //     return response()->json('Não foi possível adicionar o domínio, verifique os dados informados!');
        // }

        // Dominio::create([
        //     'projeto' => $projeto->id,
        //     'dominio' => $client->getShopManager()->get()->getDomain(),
        //     'ip_dominio' => 'Shopify',
        // ]);

        $imagem = $request->file('foto_projeto');

        if ($imagem != null) {
            $nome_foto = 'projeto_' . $projeto->id . '_.' . $imagem->getClientOriginalExtension();

            $imagem->move(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO, $nome_foto);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $nome_foto);

            $img->crop($dados['foto_w'], $dados['foto_h'], $dados['foto_x1'], $dados['foto_y1']);

            $img->resize(200, 200);

            Storage::delete('public/upload/projeto/'.$nome_foto);

            $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $nome_foto);

            $projeto->update([
                'foto' => $nome_foto
            ]);
        }

        UserProjeto::create([
            'user'              => \Auth::user()->id,
            'projeto'           => $projeto->id,
            'empresa'           => $dados['empresa'],
            'tipo'              => 'produtor',
            'responsavel_frete' => true,
            'permissao_acesso'  => true,
            'permissao_editar'  => true,
            'status'            => 'ativo'
        ]);

        $products = $client->getProductManager()->findAll([]);

        foreach($products as $product){

            $produto = Produto::create([
                'user' => \Auth::user()->id,
                'nome' => $product->getTitle(),
                'descricao' => $product->getBodyHtml(),
                'garantia' => '0',
                'disponivel' => true,
                'quantidade' => '0',
                'disponivel' => true,
                'formato' => 1,
                'categoria' => '1',
                'custo_produto' => '',
            ]);

            $img = Image::make($product->getImage()->getSrc());

            $nome_foto = 'produto_' . $produto->id . '_.png';

            Storage::delete('public/upload/produto/'.$nome_foto);

            $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nome_foto);

            $produto->update([
                'foto' => $nome_foto
            ]);

            $novo_codigo_identificador = false;

            while($novo_codigo_identificador == false){
    
                $codigo_identificador = $this->randString(3).rand(100,999);
                $plano = Plano::where('cod_identificador', $codigo_identificador)->first();
                if($plano == null){
                    $novo_codigo_identificador = true;
                }
            }

            foreach($product->getVariants() as $variant){

                $plano = Plano::create([
                    'shopify_id' => $product->getId(),
                    'shopify_variant_id' => $variant->getId(),
                    'empresa' => $dados['empresa'],
                    'projeto' => $projeto->id,
                    'nome' => $product->getTitle(),
                    'descricao' => $product->getBodyHtml(),
                    'cod_identificador' => $codigo_identificador,
                    'preco' => $variant->getPrice(),
                    'frete_fixo' => '1',
                    'valor_frete' => '0.00',
                    'pagamento_cartao' => true,
                    'pagamento_boleto' => true,
                    'status' => '1',
                    'transportadora' => '2',
                ]);
    
                $img = Image::make($product->getImage()->getSrc());
    
                $nome_foto = 'plano_' . $plano->id . '_.png';
    
                Storage::delete('public/upload/plano/'.$nome_foto);
    
                $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $nome_foto);
    
                $plano->update([
                    'foto' => $nome_foto
                ]);

            }


            ProdutoPlano::create([
                'produto' => $produto->id,
                'plano' => $plano->id,
                'quantidade_produto' => '1'
            ]);
        }

        IntegracaoShopify::create([
            'token' => $dados['token'],
            'url_loja' => $dados['url_loja'],
            'user' => \Auth::user()->id,
            'projeto' => $projeto->id
        ]);

        return response()->json('Sucesso');
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
