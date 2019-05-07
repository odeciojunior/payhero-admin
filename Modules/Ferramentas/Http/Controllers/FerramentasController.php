<?php

namespace Modules\Ferramentas\Http\Controllers;

use Slince\Shopify\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Slince\Shopify\PublicAppCredential;
use Modules\Notificacoes\Notifications\Teste;

class FerramentasController extends Controller {

    public function index() {

        // $response = \Ebanx\Ebanx::doQuery([
        //     'hash' => '5cbf6fe0b149d44bcbb4c6be6db37fd84da757cb2498492f'
        // ]);

        // dd($response);

        $credential = new PublicAppCredential('985c9fc4999e55f988a9dfd388fe6890');

        $client = new Client($credential, 'toda-bolsa.myshopify.com', [
            'metaCacheDir' => './tmp'
        ]);

        $transaction = $client->getTransactionManager()->create(1033713942610,[
            "kind"      => "capture",
        ]);

        dd($transaction);

        return view('ferramentas::index');
    }

}


// Authorization key
//     4735146656
// Message
//     Completed
// Amount
//     R$ 97,90
// Gateway
//     Mercado Pago
// Status
//     success
// Type
//     sale


// Order
//     #1200
// Authorization key
//     | 4692859636 | 4704623673 
// Message
//     Pending
// Amount
//     R$ 119,90
// Gateway
//     Mercado Pago
// Status
//     pending
// Type
//     sale
// Created
//     23 de abr de 2019 17:36

// Information from the gateway

// X account
//     8981948630474152
// X reference
//     6082986246243
// X currency
//     BRL
// X test
//     false
// X amount
//     119.90
// X result
//     pending
// X gateway reference
//     | 4692859636 | 4704623673 
// X timestamp
//     2019-04-23T20:36:18Z
// X signature
//     002fb3bcfe3f481d564b68861777e8b002ed092b0e4a9e6a6f191f0718b06a17

