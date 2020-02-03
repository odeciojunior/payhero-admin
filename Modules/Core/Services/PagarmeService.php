<?php

namespace Modules\Core\Services;

use Exception;
use Carbon\Carbon;
use Modules\Core\Entities\Sale;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use PagarMe\Client as PagarmeClient;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\SplitPaymentService;

/**
 * Class PagarmeService
 * @package Modules\Core\Services
 */
class PagarmeService
{
    private $pagarmeClient;
    private $sale;
    private $totalValue;
    private $shippingValue;
    private $client;
    private $delivery;
    private $project;
    private $saleService;
    private $transactionModel;

    /**
     * PagarmeService constructor.
     * @param Sale $sale
     * @param $totalValue
     * @param $shippingValue
     */
    public function __construct(Sale $sale, $totalValue, $shippingValue)
    {

        $this->sale             = $sale;
        $this->shippingValue    = $shippingValue;
        $this->totalValue       = $totalValue + $shippingValue;
        $this->client           = $sale->customer;
        $this->delivery         = $sale->delivery;
        $this->project          = $sale->project;
        $this->saleService      = new SaleService();
        $this->transactionModel = new Transaction();

        if (getenv('PAGAR_ME_PRODUCTION') == 'true') {
            $this->pagarmeClient = new PagarmeClient(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCTION'));
        } else {
            $this->pagarmeClient = new PagarmeClient(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }
    }

    /**
     * @param $dueDate
     * @return array
     */
    public function boletoPayment($dueDate, $discountType = null, $discountValue = null)
    {
        try {
            $transaction = $this->pagarmeClient->transactions()->create([
                                                                            'amount'                 => $this->totalValue,
                                                                            'payment_method'         => 'boleto',
                                                                            'boleto_expiration_date' => $dueDate,
                                                                            "postback_url"           => getenv('APP_ENV') == 'production' ? "https://app.cloudfox.net/postback/pagarme" : "https://app.local.net/postback/pagarme",
                                                                            "async"                  => false,
                                                                            'customer'               => [
                                                                                'external_id'   => '#' . $this->client->id,
                                                                                'name'          => $this->client->name,
                                                                                'type'          => 'individual',
                                                                                'country'       => 'br',
                                                                                'documents'     => [
                                                                                    [
                                                                                        'type'   => 'cpf',
                                                                                        'number' => preg_replace("/[^0-9]/", "", $this->client->document),
                                                                                    ],
                                                                                ],
                                                                                'phone_numbers' => [
                                                                                    '+' . preg_replace("/[^0-9]/", "", $this->client->telephone),
                                                                                ],
                                                                                'email'         => $this->client->email,
                                                                            ],
                                                                            'billing'                => [
                                                                                'name'    => $this->client->name,
                                                                                'address' => [
                                                                                    "country"       => "br",
                                                                                    "street"        => $this->delivery->street,
                                                                                    "street_number" => $this->delivery->number,
                                                                                    "state"         => $this->delivery->state,
                                                                                    "city"          => $this->delivery->city,
                                                                                    "neighborhood"  => $this->delivery->neighborhood,
                                                                                    "zipcode"       => $this->delivery->zip_code,
                                                                                ],
                                                                            ],
                                                                            'shipping'               => [
                                                                                'name'          => $this->delivery->receiver_name,
                                                                                'fee'           => str_replace('.', '', $this->shippingValue),
                                                                                'delivery_date' => Carbon::now()
                                                                                                         ->addDays(10)
                                                                                                         ->format('Y-m-d'),
                                                                                'expedited'     => false,
                                                                                'address'       => [
                                                                                    "country"       => "br",
                                                                                    "street"        => $this->delivery->street,
                                                                                    "street_number" => $this->delivery->number,
                                                                                    "state"         => $this->delivery->state,
                                                                                    "city"          => $this->delivery->city,
                                                                                    "neighborhood"  => $this->delivery->neighborhood,
                                                                                    "zipcode"       => $this->delivery->zip_code,
                                                                                ],
                                                                            ],
                                                                            'items'                  => $this->saleService->getPagarmeItensList($this->sale),
                                                                            'metadata'               => [
                                                                                'sale_id' => Hashids::encode($this->sale->id),
                                                                            ],
                                                                            'split_rules'            => [
                                                                                [
                                                                                    'recipient_id'          => getenv('PAGAR_ME_PRODUCTION') == 'true' ? getenv('PAGAR_ME_PRODUCTION_RECIPIENT_ID') : getenv('PAGAR_ME_SANDBOX_RECIPIENT_ID'),
                                                                                    'amount'                => $this->totalValue,
                                                                                    'liable'                => true,
                                                                                    'charge_processing_fee' => 'true',
                                                                                ],
                                                                            ],
                                                                        ]);
        } catch (Exception $e) {
            Log::critical('erro ao efetuar pagamento com boleto no pagar.me na venda ' . $this->sale->id);
            report($e);

            $this->sale->update([
                                    'status' => 10,
                                ]);

            return [
                'status'  => 'error',
                'message' => 'OCORREU ALGUM ERRO, TENTE NOVAMENTE EM ALGUNS MINUTOS!',
            ];
        }

        $this->sale->update([
                                'gateway_id'             => 1, //marretado 1 do pagarme
                                'gateway_transaction_id' => $transaction->tid,
                                'gateway_status'         => $transaction->status,
                                'boleto_digitable_line'  => $transaction->boleto_barcode,
                                'boleto_link'            => $transaction->boleto_url,
                                'boleto_due_date'        => $transaction->boleto_expiration_date,
                                'status'                 => 2,
                                'start_date'             => Carbon::now(),
                            ]);

        $transactions = $this->transactionModel->where('sale_id', $this->sale->id)->delete();

        $splitPaymentService = new SplitPaymentService();

        $splitPaymentService->splitPayment($this->totalValue, $this->sale, $this->project, $this->sale->user);

        return [
            'status'  => 'success',
            'message' => 'Boleto gerado com sucesso!',
        ];
    }
}




