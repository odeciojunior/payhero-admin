<?php

namespace Modules\Shopify\Http\Controllers;

use App\Plano;
use App\Venda;
use Exception;
use App\Empresa;
use App\Entrega;
use App\Produto;
use App\Projeto;
use App\Comprador;
use App\PlanoVenda;
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
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class ShopifyController extends Controller {


    public function index() {

        $empresas = Empresa::where('user',\Auth::user()->id)->get()->toArray();

        $integracoesShopify = IntegracaoShopify::where('user',\Auth::user()->id)->get()->toArray();

        $projetos = [];

        foreach($integracoesShopify as $integracaoShopify){
            $projeto = Projeto::find($integracaoShopify['projeto']);
            if($projeto){
                $projetos[] = $projeto;
            }
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

        try{
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
        }

        catch(\Exception $e){
            return response()->json('Dados do shopify inválidos, revise os dados informados');
        }
        
        $imagem = $request->file('foto_projeto');

        if ($imagem != null) {
            $nomeFoto = 'projeto_' . $projeto->id . '_.' . $imagem->getClientOriginalExtension();

            $imagem->move(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO, $nomeFoto);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $nomeFoto);

            $img->crop($dados['foto_w'], $dados['foto_h'], $dados['foto_x1'], $dados['foto_y1']);

            $img->resize(200, 200);

            Storage::delete('public/upload/projeto/'.$nomeFoto);

            $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $nomeFoto);

            $projeto->update([
                'foto' => $nomeFoto
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
                    'nome' => substr($product->getTitle(),0,100),
                    'descricao' => '',
                    'garantia' => '0',
                    'disponivel' => true,
                    'quantidade' => '0',
                    'formato' => 1,
                    'categoria' => '11',
                    'custo_produto' => '',
                ]);

                $novoCodigoIdentificador = false;

                while($novoCodigoIdentificador == false){

                    $codigoIdentificador = $this->randString(3).rand(100,999);
                    $plano = Plano::where('cod_identificador', $codigoIdentificador)->first();
                    if($plano == null){
                        $novoCodigoIdentificador = true;
                    }
                }

                $plano = Plano::create([
                    'shopify_id' => $product->getId(),
                    'shopify_variant_id' => $variant->getId(),
                    'empresa' => $dados['empresa'],
                    'projeto' => $projeto->id,
                    'nome' => substr($product->getTitle(),0,100),
                    'descricao' => '',
                    'cod_identificador' => $codigoIdentificador,
                    'preco' => $variant->getPrice(),
                    'frete_fixo' => '1',
                    'valor_frete' => '0.00',
                    'pagamento_cartao' => true,
                    'pagamento_boleto' => true,
                    'status' => '1',
                    'transportadora' => '2',
                    'qtd_parcelas' => '12',
                    'parcelas_sem_juros' => '1'
                ]);

                if(count($product->getVariants()) > 1){
                    foreach($product->getImages() as $image){

                        foreach($image->getVariantIds() as $variantId){
                            if($variantId == $variant->getId()){

                                $img = Image::make($image->getSrc());
            
                                $nomeFoto = 'plano_' . $plano->id . '_.png';
            
                                Storage::delete('public/upload/plano/'.$nomeFoto);
            
                                $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $nomeFoto);
            
                                $plano->update([
                                    'foto' => $nomeFoto
                                ]);

                                $img = Image::make($image->getSrc());
        
                                $nomeFoto = 'produto_' . $produto->id . '_.png';
                    
                                Storage::delete('public/upload/produto/'.$nomeFoto);
                    
                                $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nomeFoto);
                    
                                $produto->update([
                                    'foto' => $nomeFoto
                                ]);
        
                            }
                        }
                    }
                }
                else{

                    $img = Image::make($product->getImage()->getSrc());
            
                    $nomeFoto = 'plano_' . $plano->id . '_.png';

                    Storage::delete('public/upload/plano/'.$nomeFoto);

                    $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $nomeFoto);

                    $plano->update([
                        'foto' => $nomeFoto
                    ]);

                    $img = Image::make($product->getImage()->getSrc());

                    $nomeFoto = 'produto_' . $produto->id . '_.png';
        
                    Storage::delete('public/upload/produto/'.$nomeFoto);
        
                    $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nomeFoto);
        
                    $produto->update([
                        'foto' => $nomeFoto
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

                $descricao = '';

                try{
                    $descricao = $variant->getOption1();
                    if($descricao == 'Default Title'){
                        $descricao = '';
                    }
                    if($variant->getOption2() != ''){
                        $descricao .= ' - '. $$variant->getOption2();
                    }
                    if($variant->getOption3() != ''){
                        $descricao .= ' - '. $$variant->getOption3();
                    }
                }
                catch(\Exception $e){
                    //
                }

                if($plano == null){
                    $produto = Produto::create([
                        'user' => \Auth::user()->id,
                        'nome' => substr($product->getTitle(),0,100),
                        'descricao' => $descricao,
                        'garantia' => '0',
                        'disponivel' => true,
                        'quantidade' => '0',
                        'disponivel' => true,
                        'formato' => 1,
                        'categoria' => '1',
                        'custo_produto' => '',
                    ]);

                    $novoCodigoIdentificador = false;

                    while($novoCodigoIdentificador == false){

                        $codigoIdentificador = $this->randString(3).rand(100,999);
                        $plano = Plano::where('cod_identificador', $codigoIdentificador)->first();
                        if($plano == null){
                            $novoCodigoIdentificador = true;
                        }
                    }

                    $userProjeto = UserProjeto::where([
                        ['user', \Auth::user()->id],
                        ['projeto',$dados['projeto']],
                        ['tipo', 'produtor']
                    ])->first();

                    $plano = Plano::create([
                        'shopify_id' => $product->getId(),
                        'shopify_variant_id' => $variant->getId(),
                        'empresa' => $userProjeto->empresa,
                        'projeto' => $projeto->id,
                        'nome' => substr($product->getTitle(),0,100),
                        'descricao' => $descricao,
                        'cod_identificador' => $codigoIdentificador, 
                        'preco' => $variant->getPrice(),
                        'frete_fixo' => '1',
                        'valor_frete' => '0.00',
                        'pagamento_cartao' => true,
                        'pagamento_boleto' => true,
                        'status' => '1',
                        'transportadora' => '2',
                        'qtd_parcelas' => '12',
                        'parcelas_sem_juros' => '1'
                    ]);

                    if(count($product->getVariants()) > 1){

                        foreach($product->getImages() as $image){

                            foreach($image->getVariantIds() as $variantId){
                                if($variantId == $variant->getId()){

                                    $img = Image::make($image->getSrc());

                                    $nomeFoto = 'plano_' . $plano->id . '_.png';

                                    Storage::delete('public/upload/plano/'.$nomeFoto);

                                    $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $nomeFoto);

                                    $plano->update([
                                        'foto' => $nomeFoto
                                    ]);

                                    $img = Image::make($image->getSrc());

                                    $nomeFoto = 'produto_' . $produto->id . '_.png';

                                    Storage::delete('public/upload/produto/'.$nomeFoto);

                                    $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nomeFoto);

                                    $produto->update([
                                        'foto' => $nomeFoto
                                    ]);

                                }
                            }
                        }
                    }
                    else{

                        $img = Image::make($product->getImage()->getSrc());
                
                        $nomeFoto = 'plano_' . $plano->id . '_.png';
    
                        Storage::delete('public/upload/plano/'.$nomeFoto);
    
                        $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $nomeFoto);
    
                        $plano->update([
                            'foto' => $nomeFoto
                        ]);

                        $img = Image::make($product->getImage()->getSrc());

                        $nomeFoto = 'produto_' . $produto->id . '_.png';
            
                        Storage::delete('public/upload/produto/'.$nomeFoto);
            
                        $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO . $nomeFoto);
            
                        $produto->update([
                            'foto' => $nomeFoto
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
                        'nome' => substr($product->getTitle(),0,100),
                        'descricao' => $descricao,
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
