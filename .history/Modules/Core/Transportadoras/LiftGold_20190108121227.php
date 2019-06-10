<?php
namespace Modules\Core\Transportadoras;

use App\Produto;
use App\ProdutoPlano;
use Illuminate\Support\Facades\Log;

class LiftGold {

    
    static function realizarPedido($venda, $plano, $entrega, $comprador){

        $produtos_planos = ProdutoPlano::where("plano", $plano['id'])->get()->toArray();
       
        foreach($produtos_planos as $key => &$produtos_plano){
            $p = [];
            $produto = Produto::find($produtos_plano['produto']);
            $p['nome'] = $produto['nome'];
            $p['descrição'] = $produto['descricao']; 
            $p['quantidade'] = $produtos_plano['quantidade_produto'];  

            $produtos[] = $p;

        }

        $data = [ 
            'cod_venda'  => $venda['id'],
            'tipo_envio' => 'PAC',
            'comprador'  => [
                        'cpf'                => $comprador->cpf_cnpj,
                        'nome'               => $comprador->nome,
                        'data_nascimento'    => '01/01/2000',
                        'email'              => $comprador->email,
                        'telefone'           => $comprador->telefone,
                        'cep'                => $entrega['cep'],
                        'endereco'           => $entrega['rua'],
                        'numero'             => $entrega['numero'],
                        'bairro'             => $entrega['bairro'],
                        'cidade'             => $entrega['cidade'],
                        'estado'             => $entrega['estado'],
                        'pais'               => 'Brasil',
                        'complemento'        => $entrega['ponto_referencia'],
                        'referencia_externa' => '',
            ],

            'produtos'  => $produtos,
            
        ];
        
        $dados = json_encode($data);
        Log::write('info', "Array Dados Lift #:" . print_r($dados, true));

        $curl = curl_init();

        curl_setopt_array($curl, 
            array(
                CURLOPT_URL => "https://healthlab.herokuapp.com/data",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $dados,
                CURLOPT_HTTPHEADER => 
                array(
                    'Content-Type: application/json',
                    'Accept: application/json'
                ),
            )
        );

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            Log::write('info', "cURL Error LiftGold #:" . print_r($err, true));
            
             $response=[
                 'resposta_transportadora' => $err
             ];
        } else {
            Log::write('info', 'Pedido realizado na LiftGold1 : '. print_r($response, true));
                 $response = json_decode($response, true);
                 $update=[
                     'id_transportadora_pedido' => $response['id'],
                     'status_transportadora'    => $response['status']
                 ];
        }
        //Log::write('info', 'Pedido realizado na LiftGold3 : '. print_r($update, true));
        
        return $update;

    }

   
}   


