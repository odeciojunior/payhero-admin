<?php
namespace Modules\Core\Transportadoras;

use App\Comprador;
use Illuminate\Support\Facades\Log;

class Kapsula {

    private const TOKEN = "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjRkZjljNDY3NmFmYmU4ZjJhNGRiZjJiMWUyY2M4ZjkwODY1NmI5Y2E0ZDNmMWZlNjA1ZGExODliOWE0ZGI2YTUzZmRhNDQxZGQyZTBiMDdiIn0.eyJhdWQiOiI2IiwianRpIjoiNGRmOWM0Njc2YWZiZThmMmE0ZGJmMmIxZTJjYzhmOTA4NjU2YjljYTRkM2YxZmU2MDVkYTE4OWI5YTRkYjZhNTNmZGE0NDFkZDJlMGIwN2IiLCJpYXQiOjE1MzU4MzM1MzcsIm5iZiI6MTUzNTgzMzUzNywiZXhwIjoxNTY3MzY5NTM3LCJzdWIiOiIzNyIsInNjb3BlcyI6W119.aJRSK58Wl-0dnhUIEcpNpZ6uyKEjGHyI0jm3zq4QMqARYC06k5ACH4rOM2FWXd4agRzQfoecLeiDEjjeRnaQmC6BkNJP1GTlDuT-sQWUi3LVAKnpsFG81eCjaH9sybKSn7VSx3XrQd02XNuLmTnc_IejdNWeoXX-dRUCQQyjh4esvmapsX5lCl9g_K4kwigI7CGV_DH7YCFOmQH0PC4J2uuvdx9KlppfxTGTiTT1bJhVUztKTZdRm4wJdnxM4nn2w3mY3ByEDVo3qz0XnT3djwuwBDCw7osb0R9_EASwwwoNv-OUHmMEh2hedm-60wryAQyhNPhwnFwPyUGzeYDKeYWtD-qaqs8kekYorxsYd9gBo_qIrcnGq33Vwsw4daYJqEq0RGgsa9_P6-UcLqp7hiss5KO_NRlGchAdyrsh0zgbQIvuCK3PYw2HFhieMiY3OSiqtuXBrSqT-i3FK8SInf0GV8N8vZ3vs4WTXgIpz12BeuA3SCh2j6yf0P2RXswRz43szEpCAsGTyQgvpXZrkh_ZQerhBc_45nHPAPlb0GWi6Vx1VSfm9lFPwBHluc60hgXDPCHLS75rQOQfMW0C3LZed6YVLpIXqyFXRfPOndxL4NBabXqyDXicyZUGe7lKca85Rll5LgNGQFBvnEyG3h2K-0vjKLvP1_DPwEO98DA";
    
    static function cadastarCliente($entrega, $comprador){

        $dados_comprador = [
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
            'referencia_externa' => ''
        ];
        
        $curl = curl_init();

        curl_setopt_array($curl, 
            array(
                CURLOPT_URL => "https://ev.kapsula.com.br/api/v1/clientes",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($dados_comprador),
                CURLOPT_HTTPHEADER => 
                array(
                    'Authorization: '. self::TOKEN,
                    'Content-Type: application/json'
                ),
            )
        );

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            Log::write('info', "cURL Error #:" . $err);
        } else {
            $response = json_decode($response);

            if(isset($response->cliente_id)){
                $cliente_id =  $response->cliente_id;
                Log::write('info', 'cliente já cadastrado : '. $cliente_id);
                $comprador_up = Comprador::find($comprador->id);
                $comprador_up->update([
                    'id_kapsula_cliente' => $cliente_id,
                ]);
            }
            if(isset($response->cliente)){
                $cliente_id =  $response->cliente;
                Log::write('info', 'cliente novo : '. $cliente_id);
                $comprador_up = Comprador::find($comprador->id);
                $comprador_up->update([
                    'id_kapsula_cliente' => $cliente_id,
                ]);
            }
        }
        return $cliente_id;

    }

    static function realizarPedido($cliente_id, $id_pacote_kapsula){
        
        $dados_transporte = [
            "cliente_id" => $cliente_id,
            "pacote_id"  => $id_pacote_kapsula,
            "tipo_frete" => 0
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(

        CURLOPT_URL => "https://ev.kapsula.com.br/api/v1/pedidos",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($dados_transporte),
            CURLOPT_HTTPHEADER => array(
                'Authorization: '.self::TOKEN,
                'Content-Type: application/json',
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            Log::write('info', "cURL Error #:" . $err);
            $entrega=[
                'resposta_transportadora' => $err
            ];
        } else {
            $response = json_decode($response);
            Log::write('info', 'entrega confirmada na kapsula : '. print_r($response, true));

            $entrega=[
                'id_transportadora_pedido' => $response->pedido,
                'status_transportadora'    => $response->code,
                'resposta_transportadora' => $response->message
            ];
      
        }
        return $entrega;
    }

    static function rastrearPedido($entrega){
                

        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            //1806873   1811973   /clientes/11604
        CURLOPT_URL => "https://ev.kapsula.com.br/api/v1/pedidos/".$entrega,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Authorization: '.self::TOKEN,
                'Content-Type: application/json',
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return $response;
        
        //criando o recurso cURL
        $cr = curl_init();
        
        //definindo a url de busca 
        curl_setopt($cr, CURLOPT_URL, "https://ev.kapsula.com.br/rastrear/".$entrega);
        
         //definindo a url de busca 
         curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);
         
         //definindo uma variável para receber o conteúdo da página...
         $response = curl_exec($cr);
         $err = curl_error($cr);

         //fechando-o para liberação do sistema.
         curl_close($cr); //fechamos o recurso e liberamos o sistema...
         
         if ($err) {
             $retorno = [];
             $retorno = [
                 'error' => true,
                 'message'    => $err
             ];
         } else {
             
             $retorno = $response;
         }
 
         return $retorno;




    }
}   