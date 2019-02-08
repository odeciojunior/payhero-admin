<?php

namespace Modules\Shopify\Http\Controllers;

use App\Plano;
use Exception;
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

        try{
            $credential = new PublicAppCredential($dados['token']);
            //$credential = new PublicAppCredential('0fc0fea41cc38c989749dc9040794bb4');

            //$client = new Client($credential, 'canto-infantil.myshopify.com', [
            $client = new Client($credential, $dados['url_loja'], [
                'metaCacheDir' => './tmp' // Metadata cache dir, required
            ]);
        }
        catch(\Exception $e){
            return response()->json('Dados do shopify inválidos, revise os dados informados');
        }

        $projeto = Projeto::create([
            'nome' => $client->getShopManager()->get()->getName(),
            'status' => '1',
            'visibilidade' => 'privado',
            'porcentagem_afiliados' => '0',
            'descricao' =>  $client->getShopManager()->get()->getName(),
            'descricao_fatura' => $client->getShopManager()->get()->getName(),
            'url_pagina' =>  'https://'.$client->getShopManager()->get()->getDomain(),
            'afiliacao_automatica' => false,
            'shopify_id' => $client->getShopManager()->get()->getId(),
        ]);

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

            foreach($product->getVariants() as $variant){

                $produto = Produto::create([
                    'user' => \Auth::user()->id,
                    'nome' => $product->getTitle(),
                    'descricao' => substr($product->getBodyHtml(),0,240),
                    'garantia' => '0',
                    'disponivel' => true,
                    'quantidade' => '0',
                    'formato' => 1,
                    'categoria' => '1',
                    'custo_produto' => '',
                ]);

                $novo_codigo_identificador = false;

                while($novo_codigo_identificador == false){

                    $codigo_identificador = $this->randString(3).rand(100,999);
                    $plano = Plano::where('cod_identificador', $codigo_identificador)->first();
                    if($plano == null){
                        $novo_codigo_identificador = true;
                    }
                }

                $plano = Plano::create([
                    'shopify_id' => $product->getId(),
                    'shopify_variant_id' => $variant->getId(),
                    'empresa' => $dados['empresa'],
                    'projeto' => $projeto->id,
                    'nome' => $product->getTitle(),
                    'descricao' => substr($product->getBodyHtml(),0,240),
                    'cod_identificador' => $codigo_identificador,
                    'preco' => $variant->getPrice(),
                    'frete_fixo' => '1',
                    'valor_frete' => '0.00',
                    'pagamento_cartao' => true,
                    'pagamento_boleto' => true,
                    'status' => '1',
                    'transportadora' => '2',
                ]);

                if(count($product->getVariants()) > 1){
                    foreach($product->getImages() as $image){

                        foreach($image->getVariantIds() as $variant_id){
                            if($variant_id == $variant->getId()){

                                $img = Image::make($image->getSrc());
            
                                $nome_foto = 'plano_' . $plano->id . '_.png';
            
                                Storage::delete('public/upload/plano/'.$nome_foto);
            
                                $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $nome_foto);
            
                                $plano->update([
                                    'foto' => $nome_foto
                                ]);

                                $img = Image::make($image->getSrc());
        
                                $nome_foto = 'produto_' . $produto->id . '_.png';
                    
                                Storage::delete('public/upload/produto/'.$nome_foto);
                    
                                $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nome_foto);
                    
                                $produto->update([
                                    'foto' => $nome_foto
                                ]);
        
                            }
                        }
                    }
                }
                else{

                    $img = Image::make($product->getImage()->getSrc());
            
                    $nome_foto = 'plano_' . $plano->id . '_.png';

                    Storage::delete('public/upload/plano/'.$nome_foto);

                    $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $nome_foto);

                    $plano->update([
                        'foto' => $nome_foto
                    ]);

                    $img = Image::make($product->getImage()->getSrc());

                    $nome_foto = 'produto_' . $produto->id . '_.png';
        
                    Storage::delete('public/upload/produto/'.$nome_foto);
        
                    $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nome_foto);
        
                    $produto->update([
                        'foto' => $nome_foto
                    ]);
                }

                ProdutoPlano::create([
                    'produto' => $produto->id,
                    'plano' => $plano->id,
                    'quantidade_produto' => '1'
                ]);
            }

        }

        IntegracaoShopify::create([
            'token' => $dados['token'],
            'url_loja' => $dados['url_loja'],
            'user' => \Auth::user()->id,
            'projeto' => $projeto->id
        ]);

        return response()->json('Sucesso');
    }

    public function sincronizarIntegracao(Request $request){

        $dados = $request->all();

        $projeto = Projeto::find($dados['projeto']);
        $integracao = IntegracaoShopify::where('projeto',$dados['projeto'])->first();

        try{
            $credential = new PublicAppCredential($integracao['token']);

            $client = new Client($credential, $integracao['url_loja'], [
                'metaCacheDir' => './tmp'
            ]);
        }
        catch(\Exception $e){
            return response()->json('Dados do shopify inválidos, revise os dados informados');
        }

        $products = $client->getProductManager()->findAll([]);

        foreach($products as $product){

            foreach($product->getVariants() as $variant){

                $plano = Plano::where('shopify_variant_id' , $variant->getId())->first();

                if($plano == null){
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
                            
                    $novo_codigo_identificador = false;

                    while($novo_codigo_identificador == false){

                        $codigo_identificador = $this->randString(3).rand(100,999);
                        $plano = Plano::where('cod_identificador', $codigo_identificador)->first();
                        if($plano == null){
                            $novo_codigo_identificador = true;
                        }
                    }

                    $user_projeto = UserProjeto::where([
                        ['user', \Auth::user()->id],
                        ['projeto',$dados['projeto']],
                        ['tipo', 'produtor']
                    ])->first();

                    $plano = Plano::create([
                        'shopify_id' => $product->getId(),
                        'shopify_variant_id' => $variant->getId(),
                        'empresa' => $user_projeto->empresa,
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

                    if(count($product->getVariants()) > 1){

                        foreach($product->getImages() as $image){

                            foreach($image->getVariantIds() as $variant_id){
                                if($variant_id == $variant->getId()){

                                    $img = Image::make($image->getSrc());
                
                                    $nome_foto = 'plano_' . $plano->id . '_.png';
                
                                    Storage::delete('public/upload/plano/'.$nome_foto);
                
                                    $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $nome_foto);
                
                                    $plano->update([
                                        'foto' => $nome_foto
                                    ]);

                                    $img = Image::make($image->getSrc());
            
                                    $nome_foto = 'produto_' . $produto->id . '_.png';
                        
                                    Storage::delete('public/upload/produto/'.$nome_foto);
                        
                                    $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nome_foto);
                        
                                    $produto->update([
                                        'foto' => $nome_foto
                                    ]);
            
                                }
                            }
                        }
                    }
                    else{

                        $img = Image::make($product->getImage()->getSrc());
                
                        $nome_foto = 'plano_' . $plano->id . '_.png';
    
                        Storage::delete('public/upload/plano/'.$nome_foto);
    
                        $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $nome_foto);
    
                        $plano->update([
                            'foto' => $nome_foto
                        ]);

                        $img = Image::make($product->getImage()->getSrc());

                        $nome_foto = 'produto_' . $produto->id . '_.png';
            
                        Storage::delete('public/upload/produto/'.$nome_foto);
            
                        $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nome_foto);
            
                        $produto->update([
                            'foto' => $nome_foto
                        ]);

                    }

                    ProdutoPlano::create([
                        'produto' => $produto->id,
                        'plano' => $plano->id,
                        'quantidade_produto' => '1'
                    ]);
                }
                else{
                    $plano->update([
                        'nome' => $product->getTitle(),
                        'descricao' => $product->getBodyHtml(),
                        'preco' => $variant->getPrice(),
                    ]);
                }
            }

        }

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
