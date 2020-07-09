<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Laracasts\Presenter\Exceptions\PresenterException;
use App\Entities\Company;
use App\Entities\Gateway;
use App\Entities\GatewayPostback;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;

/**
 * Class GetnetService
 * @package App\Services
 */
class GetnetService
{
    public const URL_API = 'https://api-homologacao.getnet.com.br/';

    private $accessToken;
    private $authorizationToken;
    private $gatewayId;
    private $sendData = [];
    private $gatewayResult = [];
    private $exceptions = [];

    public function __construct()
    {
        $gateway = Gateway::where("name", "getnet_sandbox")->first();
        $configs = json_decode(FoxUtilsService::xorEncrypt($gateway->json_config, "decrypt"), true);
        $this->gatewayId = $gateway->id ?? null;
        $this->authorizationToken = base64_encode($configs['public_token'] . ':' . $configs['private_token']);
        $this->setAccessToken();
    }

    public function getAuthorizationHeader()
    {
        return [
            'authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
            'seller_id: ' . env('GET_NET_SELLER_ID'),
        ];
    }

    public function setAccessToken()
    {
        $headers = [
            'content-type: application/x-www-form-urlencoded',
            'authorization: Basic ' . $this->authorizationToken,
        ];

        $curl = curl_init(self::URL_API . 'auth/oauth/v2/token');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'scope=oob&grant_type=client_credentials');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpStatus == 200) {
            $this->accessToken = json_decode($result)->access_token;
        } else {
            throw new Exception('Erro ao gerar token de acesso captura getnet');
        }
    }

    /**
     * Method POST
     * Tokeniza cartão
     * @param string $cardNumber
     * @param string $customerId
     */
    public function tokenizeCard($cardNumber, $customerId)
    {
        $data = $this->sendCurl('v1/tokens/card', 'POST', ['card_number' => $cardNumber, 'customer_id' => $customerId]);
        return json_decode($data);
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $data
     */
    private function sendCurl($url, $method, $data = null)
    {
        $curl = curl_init(self::URL_API . $url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        if(!is_null($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader());
        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $result;
    }

    /**
     * @param $data
     * @return Sale|Sale|mixed
     * @throws Exception
     */
    public function creditCardPayment($data)
    {
        try {
            // dd($data);
            $this->sendData      = json_encode($this->getDataPayment($data));
            $this->gatewayResult = $this->sendCurl('v1/payments/credit', 'POST', $this->getDataPayment($data));
            $return = json_decode($this->gatewayResult, 1);
            // dd($return);
            if (!empty($return['status']) && $return['status'] == 'APPROVED') {
                $message = 'Cobrada com sucesso!';
            } else {
                $message = $return['message'] ?? null;
            }

            $gatewayTransactionId = null;
            if(!empty($return['payment_id'])) {
                $gatewayTransactionId = $return['payment_id'];
            } elseif(!empty($return['details'][0]['payment_id'])) {
                $gatewayTransactionId = $return['details'][0]['payment_id'];
            }

            $gatewayStatus = null;
            if(!empty($return['status'])) {
                $gatewayStatus = $return['status'];
            } elseif(!empty($return['details'][0]['status'])) {
                $gatewayStatus = $return['details'][0]['status'];
            }

            return [
                'gateway_id'                 => $this->gatewayId,
                'gateway_transaction_id'     => $gatewayTransactionId,
                'status'                     => $this->getSaleStatus($gatewayStatus),
                'gateway_status'             => $gatewayStatus,
                'gateway_card_flag'          => $return['credit']['brand'] ?? null,
                'flag'                       => $return['credit']['brand'] ?? null,
                'message'                    => $message,
                // // braspag
                // 'gateway_proof_of_sale'      => $formatedGatewayResponse['gateway_proof_of_sale'] ?? null,
                // 'gateway_tid'                => $formatedGatewayResponse['gateway_tid'] ?? null,
                // 'gateway_authorization_code' => $formatedGatewayResponse['gateway_authorization_code'] ?? null,
                // 'gateway_received_date'      => $formatedGatewayResponse['gateway_received_date'] ?? null,
            ];

        } catch (Exception $e) {
            // dd($e);
            throw $e;
        }

    }

    /**
     * @param $data
     * @return array
     */
    private function getDataPayment($data)
    {
        $itens = [];

        // dd($data);
        $amount = $data['total_value'];
        // $amount = '400';
        // if(!FoxUtilsService::isProduction() && $data['installments'] > 1) {
        //     $amount .= '0'.$data['installments'].'0'.$data['installments'];
        // }

        $marketplaceSubsellers = [];

        foreach ($data['subseller'] as $key => $value) {
            // dd($value);
            $marketplaceSubsellers[] = [
                "subseller_sales_amount" => $value['value'], // TODO Parte do Valor da loja em relação ao Pedido (em centavos) int
                "subseller_id" => $value['subseller_id'],
                "order_items" => [
                    [
                        "amount"      => $value['value'],
                        "currency"    => "BRL",
                        "id"          => $data['cart_items'][0]["sku"],
                        "description" => $data['cart_items'][0]["product_name"],
                        "tax_amount"  => $value['tax'] ?? 0,
                        // "tax_percent" => 0,
                    ],
                ],
            ];
        }

        return [
            "seller_id" => env('GET_NET_SELLER_ID'),
            "amount"    => $amount,
            "currency"  => "BRL",
            "order"     => [
                "order_id" => $idSale = Hashids::connection('sale_id')->encode($data['id']),
                // "product_type" => "service", // TODO cash_carry, digital_content, digital_goods, digital_physical, gift_card, physical_goods, renew_subs, shareware, service
            ],
            "customer"  => [
                "customer_id"     => Hashids::encode($data['client']['id']),
                "first_name"      => FoxUtilsService::splitName($data['client']['name'])[0],
                "last_name"       => FoxUtilsService::splitName($data['client']['name'])[1],
                "name"            => $data['client']['name'],
                "email"           => $data['client']['email'] ?? null,
                "document_type"   => "", // TODO
                "document_number" => $data['client']['document'],
                "phone_number"    => $data['client']['telephone'],
                "billing_address" => [
                    "street"      => $data['delivery']['street'],
                    "number"      => $data['delivery']['number'],
                    "complement"  => $data['delivery']['complement'],
                    "district"    => $data['delivery']['neighborhood'],
                    "city"        => $data['delivery']['city'],
                    "state"       => $data['delivery']['state'],
                    "country"     => $data['delivery']['country'],
                    "postal_code" => $data['delivery']['zip_code'],
                ],
            ],
            "device"    => [
                "ip_address" => "", // TODO
                "device_id"  => $data['cybersource_fingerprint'],
            ],
            "shippings" => [
                [
                    "first_name"      => FoxUtilsService::splitName($data['client']['name'])[0],
                    "name"            => $data['client']['name'],
                    "email"           => $data['client']['email'] ?? null,
                    "phone_number"    => $data['client']['telephone'],
                    "shipping_amount" => 0,
                    "address"         => [
                        "street"      => $data['delivery']['street'],
                        "number"      => $data['delivery']['number'],
                        "complement"  => $data['delivery']['complement'],
                        "district"    => $data['delivery']['neighborhood'],
                        "city"        => $data['delivery']['city'],
                        "state"       => $data['delivery']['state'],
                        "country"     => $data['delivery']['country'],
                        "postal_code" => $data['delivery']['zip_code'],
                    ],
                ],
            ],
            "credit"    => [
                "delayed"             => false,
                "pre_authorization"   => false,
                "save_card_data"      => false,
                "transaction_type"    => $data['installments'] > 1 ? "INSTALL_NO_INTEREST" : "FULL",
                // TODO FULL -  a vista, INSTALL_NO_INTEREST - parcelado sem juro, INSTALL_WITH_INTEREST - parc c/ juro
                "number_installments" => $data['installments'],
                // "soft_descriptor"     => "",
                // "dynamic_mcc"         => "",
                "card"                => [
                    "number_token"     => $data['card_token'],
                    "cardholder_name"  => $data['card_name'],
                    "security_code"    => $data['card_cvv'],
                    "brand"            => $data['card_brand'],
                    "expiration_month" => $data['card_expiration_date_month'],
                    "expiration_year"  => substr($data['card_expiration_date_year'],2),
                ],
            ],
            "marketplace_subseller_payments" => $marketplaceSubsellers,
            // [
            //     [
            //         // "subseller_sales_amount" => $data['total_value'], // TODO Parte do Valor da loja em relação ao Pedido (em centavos) int
            //         "subseller_sales_amount" => $amount, // TODO Parte do Valor da loja em relação ao Pedido (em centavos) int
            //         "subseller_id" => $data['subseller_getnet_id'],
            //         "order_items" => $itens,
            //     ]
            // ],
        ];
    }

    /**
     * @return array
     */
    public function getGatewayRequest()
    {
        try {
            return [
                "gateway_id"         => $this->gatewayId,
                "send_data"          => $this->sendData,
                "gateway_result"     => $this->gatewayResult,
                "gateway_exceptions" => json_encode([]),
            ];
        } catch (Exception $ex) {
            report($ex);

            return [];
        }
    }

    /**
     * @param array $data
     */
    public function verifyCard($data)
    {
        /*
            [
                "number_token"     => '',
                "expiration_month" => '',
                "expiration_year"  => '',
            ]
        */
        return json_decode($this->sendCurl('v1/cards/verification', 'POST', $data));
    }

    /**
     * @param string $paymentId
     */
    public function cancelPaymentDay($paymentId)
    {
        return json_decode($this->sendCurl('v1/payments/credit/'.$paymentId.'/cancel', 'POST'));
    }

    /**
     * @param $gatewayStatus
     * @return string
     * @throws Exception
     */
    public function getSaleStatus($gatewayStatus)
    {
        try {
            switch ($gatewayStatus) {
                // case 0:
                //     $paymentStatus = 'in_proccess';
                //     break;
                case 'APPROVED':
                case 'AUTHORIZED':
                case 'PAID':
                case 'CONFIRMED':
                    $paymentStatus = 'paid';
                    break;
                case 'DENIED':
                case 'NOT APPROVED':
                    $paymentStatus = 'refused';
                    break;
                // case 10:
                //     $paymentStatus = 'refunded';
                //     break;
                case 'PENDING':
                    $paymentStatus = 'pending';
                    break;
                default:
                    $paymentStatus = null;

                // case 'CANCELED':
                // case 'ERROR':

            }

            if ($paymentStatus == null) {
                report(new Exception('Status nulo, gatewayStatus: ' . $gatewayStatus));
            }

            return $paymentStatus;
        } catch (Exception $ex) {
            $this->exceptions[] = $ex->getMessage();
            throw $ex;
        }
    }

    public function teste()
    {
        $this->gatewayResult = $this->sendCurl('v1/payments/credit/5ef5683d-d383-4b47-8849-aafcda32aca6', 'GET');
    }

    /**
     * @param string $paymentId
     * @param int $amount
     * @param string $cancelCustomKey
     * @param array $dataPartial
     */
    public function cancelPayment($paymentId, $amount, $cancelCustomKey = null, $dataPartial = null)
    {
        $data = array_filter([
                                 "payment_id"                     => $paymentId,
                                 "cancel_amount"                  => $amount,
                                 "cancel_custom_key"              => $cancelCustomKey,
                                 "marketplace_subseller_payments" => $dataPartial
                                 // [
                                 //     [
                                 //         "subseller_sales_amount" => 10202,
                                 //         "subseller_id"           => 10,
                                 //         "order_items"            => [
                                 //             [
                                 //                 "amount"      => 10202,
                                 //                 "currency"    => "BRL",
                                 //                 "id"          => "X0001",
                                 //                 "description" => "Produto x",
                                 //                 "tax_percent" => 0.1,
                                 //                 "tax_amount"  => 100
                                 //             ]
                                 //         ]
                                 //     ]
                                 // ]
                             ]);

        return json_decode($this->sendCurl('v1/payments/cancel/request', 'POST', $data));
    }

    /**
     * @param string $paymentId
     * @param array $data
     */
    public function confirmationLatePayment($paymentId, $data)
    {
        /*
        [
            "amount" => 19990,
            "marketplace_subseller_payments" => [
                [
                    "subseller_sales_amount" => 10202,
                    "subseller_id" => 10,
                    "order_items" => [
                        [
                            "amount"      => 10202,
                            "currency"    => "BRL",
                            "id"          => "X0001",
                            "description" => "Produto x",
                            "tax_percent" => 0.1,
                            "tax_amount"  => 100
                        ]
                    ]
                ]
            ]
        ]
        */

        return json_decode($this->sendCurl('v1/payments/credit/'.$paymentId.'/confirm', 'POST', $data));
    }


    /**
     * @param string $paymentId
     * @param string $dateRelease (YYYY-MM-DDTHH:MM:SSZ)
     * @param string $subseller
     * @param string $productId
     * @param int $amount
     * @param bool $updateReleaseDate
     */
    public function releasePaymentToSeller($paymentId, $dateRelease, $subseller, $productId, $amount, $updateReleaseDate = false)
    {
        $data = [
            "release_payment_date" => $dateRelease,
            "subseller_id"         => $subseller,
            "order_item_release"   => [
                "id"     => $productId,
                "amount" => $amount
            ]
        ];
        $method = ($updateReleaseDate == true) ? 'PATCH' : 'POST';
        return json_decode($this->sendCurl('v1/marketplace/payments/'.$paymentId.'/release', $method, $data));
    }


    /**
     * @param array $data
     */
    public function billetPayment($data)
    {
        try {
            $itens = [];
            // dd($data);

            $marketplaceSubsellers = [];

            foreach ($data['subseller'] as $key => $value) {
                // dd($value);
                $marketplaceSubsellers[] = [
                    "subseller_sales_amount" => $value['value'], // TODO Parte do Valor da loja em relação ao Pedido (em centavos) int
                    "subseller_id" => $value['subseller_id'],
                    "order_items" => [
                        [
                            "amount"      => $value['value'],
                            "currency"    => "BRL",
                            "id"          => $data['cart_items'][0]["sku"],
                            "description" => $data['cart_items'][0]["product_name"],
                            "tax_amount"  => $value['tax'] ?? 0,
                            // "tax_percent" => 0,
                        ],
                    ],
                ];
            }

            $dataBillet = [
                "seller_id" => env('GET_NET_SELLER_ID'),
                "amount"    => $data['total_value'],
                "currency"  => "BRL",
                "order"     => [
                    "order_id" => $idSale = Hashids::connection('sale_id')->encode($data['id']),
                    // "product_type" => "service", // TODO cash_carry, digital_content, digital_goods, digital_physical, gift_card, physical_goods, renew_subs, shareware, service
                ],
                "customer"  => [
                    "first_name"      => FoxUtilsService::splitName($data['client']['name'])[0],
                    "name"            => $data['client']['name'],
                    "document_type"   => "CPF", // CPF, CNPJ TODO
                    "document_number" => $data['client']['document'],
                    "phone_number"    => $data['client']['telephone'],
                    "billing_address" => [
                        "street"      => $data['delivery']['street'],
                        "number"      => $data['delivery']['number'],
                        "complement"  => $data['delivery']['complement'],
                        "district"    => $data['delivery']['neighborhood'],
                        "city"        => $data['delivery']['city'],
                        "state"       => $data['delivery']['state'],
                        "postal_code" => $data['delivery']['zip_code'],
                    ],
                ],
                "marketplace_subseller_payments" => $marketplaceSubsellers,
                "boleto" => [
                    // "document_number"           => "1", // TODO
                    "expiration_date"           => Carbon::parse($data["due_date"])->format('d/m/Y'),
                    "instructions"              => "Não receber após o vencimento", // TODO
                    "provider"                  => "santander",
                    "guarantor_document_type"   => ($data['company_type'] == 1) ? "CPF" : "CNPJ",
                    "guarantor_document_number" => $data['company_document'],
                    "guarantor_name"            => $data['fantasy_name'],
                ]
            ];

            // dd($dataBillet['boleto']);

            $this->sendData      = json_encode($dataBillet);
            $this->gatewayResult = $this->sendCurl('v1/payments/boleto', 'POST', $dataBillet);

            $return = json_decode($this->gatewayResult, 1);
            if (!empty($return['status']) && $return['status'] == 'PENDING') {
                $saleArray = [
                    'gateway_id'                 => $this->gatewayId,
                    'gateway_transaction_id'     => $return['payment_id'],
                    'status'                     => $this->getSaleStatus($return['status']),
                    'gateway_status'             => $return['status'],
                    'boleto_digitable_line'      => $return['boleto']['typeful_line'] ?? null,
                    'boleto_link'                => (!empty($return['boleto']['_links'][0]['href'])) ? self::URL_API . $return['boleto']['_links'][0]['href'] : null,
                    'boleto_due_date'            => $return['boleto']['expiration_date'] ?? null,
                    'status'                     => 2,
                ];

            } else {
                return [
                    'status'   => 'error',
                    'message'  => $return['message'] ?? 'OCORREU UM ERRO AO GERAR O BOLETO, TENTE EM INSTANTES!',
                    'response' => [
                        'status'     => 99,
                        // 'gateway_id' => $this->getGatewayId(),
                    ],
                ];
            }

            return [
                'status'   => 'success',
                'message'  => 'Boleto gerado com sucesso!',
                'response' => $saleArray,
            ];


        } catch (Exception $e) {
            $this->exceptions[] = $e->getMessage();
            return [
                'status'   => 'error',
                'message'  => 'OCORREU UM ERRO INESPERADO, TENTE NOVAMENTE EM ALGUNS INSTANTES!',
                'response' => [
                    'status'     => 99,
                    // 'gateway_id' => $this->getGatewayId(), //TODO
                ],
            ];
        }
    }

    /**
     * @param string $cancelCustomKey
     */
    public function getCancellationByCustomKey($cancelCustomKey)
    {
        return json_decode($this->sendCurl('v1/payments/cancel/request?cancel_custom_key='.$cancelCustomKey, 'GET'));
    }

    /**
     * @param string $cancelCustomKey
     */
    public function getCancellationByRequestId($cancelRequestId)
    {
        return json_decode($this->sendCurl('v1/payments/cancel/request/'.$cancelRequestId, 'GET'));
    }

    /**
     * @param array $data
     */
    public function addCardSafeBox($data)
    {
        /* $data = [
            "number_token"              => "dfe05208b105578c070f8",
            "cardholder_name"           => "JOAO DA SILVA",
            "expiration_month"          => "12",
            "expiration_year"           => "20",
            "customer_id"               => "customer_21081826",
            "cardholder_identification" => "12345678912",
            "verify_card"               => false,
            "security_code"             => "123"
        ]; */
        return json_decode($this->sendCurl('v1/cards', 'POST', $data));
    }

    /**
     * @param string $customeId
     * @param string $ststus | all, active, renewed
     * @return object
     */
    public function listCardsSafeBox($customerId, $status = null)
    {
        $url = 'v1/cards?customer_id=' . $customerId;
        $url = (!is_null($status)) ? $url . '&status=' . $status : $url;
        return json_decode($this->sendCurl($url, 'GET'));
    }

    /**
     * @param string $cardId
     * @return object
     */
    public function getCardSafeBox($cardId)
    {
        return json_decode($this->sendCurl('v1/cards/' . $cardId, 'GET'));
    }

    /**
     * @param string $cardId
     */
    public function removeCardSafeBox($cardId)
    {
        return json_decode($this->sendCurl('v1/cards/' . $cardId, 'DELETE'));
    }

    /**
     * @param string $paymentId
     * @param array $data
     */
    public function adjustPreAuthorizationValue($paymentId, $data)
    {
        /*[
            "amount"          => 19990,
            "currency"        => "BRL",
            "soft_descriptor" => "LOJA*TESTE*COMPRA-123",
            "dynamic_mcc"     => 1799,
            "marketplace_subseller_payments" => [
                [
                    "subseller_sales_amount" => 10202,
                    "subseller_id" => 10,
                    "order_items"  => [
                        [
                            "amount"      => 10202,
                            "currency"    => "BRL",
                            "id"          => "X0001",
                            "description" => "Produto x",
                            "tax_percent" => 0.1,
                            "tax_amount"  => 100
                        ]
                    ]
                ]
            ]
        ]*/

        return json_decode($this->sendCurl('v1/payments/credit/'.$paymentId.'/adjustment', 'POST', $data));
    }

    /**
     * @param $data
     * @return bool
     * @throws Exception
     */
    public function savePostback($data)
    {
        try {
            $gatewayPostbackModel = new GatewayPostback();

            $jsonData = json_encode($data);

            $gatewayPostback = $gatewayPostbackModel->create([
                                                                 'data'                => $jsonData,
                                                                 'gateway_id'          => 5, // TODO
                                                                 'gateway_enum'        => $gatewayPostbackModel->present()
                                                                     ->gatewayEnum('getnet'), // TODO
                                                                 'processed_flag'      => false,
                                                                 'postback_valid_flag' => false,
                                                             ]);
            if ($gatewayPostback) {

                return true;
            } else {
                return false;
            }
        } catch (Exception $ex) {
            $this->exceptions[] = $ex->getMessage();
            throw $ex;
        }
    }


}