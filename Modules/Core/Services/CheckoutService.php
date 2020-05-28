<?php

namespace Modules\Core\Services;

use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Affiliate;
use SendGrid;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Transfer;
use SendGrid\Mail\Mail;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\RegeneratedBillet;

/**
 * Class CheckoutService
 * @package Modules\Core\Services
 */
class CheckoutService
{
    /**
     * @var string
     */
    private $internalApiToken;

    /**
     * @param string|null $projectId
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @param string|null $client
     * @return mixed
     * @throws PresenterException
     */
    public function getAbandonedCart(string $projectId = null, string $dateStart = null, string $dateEnd = null, string $client = null)
    {
        $checkoutModel  = new Checkout();
        $domainModel    = new Domain();
        $affiliateModel = new Affiliate();

        $affiliate = $affiliateModel->where('project_id', $projectId)
                                    ->where('user_id', auth()->user()->account_owner_id)->first();

        $abandonedCarts = $checkoutModel->whereIn('status_enum', [
            $checkoutModel->present()->getStatusEnum('recovered'),
            $checkoutModel->present()->getStatusEnum('abandoned cart'),
        ])->where('project_id', $projectId);

        if (!empty($affiliate)) {
            $abandonedCarts->where('affiliate_id', $affiliate->id);
        }

        if (!empty($client)) {
            $abandonedCarts->where('client_name', 'like', '%' . $client . '%');
        }

        if (!empty($dateStart) && !empty($dateEnd)) {
            $abandonedCarts->whereBetween('checkouts.created_at', [$dateStart, $dateEnd]);
        } else {
            if (!empty($dateStart)) {
                $abandonedCarts->whereDate('checkouts.created_at', '>=', $dateStart);
            }
            if (!empty($dateEnd)) {
                $abandonedCarts->whereDate('checkouts.created_at', '<', $dateEnd);
            }
        }

        return $abandonedCarts->with([
                                         'project.domains' => function($query) use ($domainModel) {
                                             $query->where('status', $domainModel->present()->getStatus('approved'));
                                         },
                                         'checkoutPlans.plan',
                                     ])->orderBy('id', 'DESC')->paginate(10);
    }

    /**
     * @param null $checkoutPlans
     * @return float|int
     */
    public function getSubTotal($checkoutPlans = null)
    {
        $total = 0;
        foreach ($checkoutPlans as $checkoutPlan) {
            if (!empty($checkoutPlan->plan)) {
                $total += intval(preg_replace("/[^0-9]/", "",
                                              $checkoutPlan->plan->price)) * intval($checkoutPlan->amount);
            }
        }

        return $total;
    }

    public function cancelPayment($sale, $refundAmount, $partialValues = [])
    {
        try {
            $saleService      = new SaleService();
            $transactionModel = new Transaction();
            $transferModel    = new Transfer();
            $companyModel     = new Company();
            $saleAmount       = Str::replaceFirst(',', '',
                                                  Str::replaceFirst('.', '', Str::replaceFirst('R$ ', '', $sale->total_paid_value)));
            // TODO não estamos implementando devolução parcial, quando for implementar tirar '|| $refundAmount < $saleAmount'
            // if ($refundAmount > $saleAmount || $refundAmount < $saleAmount) {
            if ($refundAmount > $saleAmount) {
                $result = [
                    'status'  => 'error',
                    'message' => 'Valor não confere com o da Venda.',
                ];
            }
            if (FoxUtils::isProduction()) {
                $urlCancelPayment = 'https://checkout.cloudfox.net/api/payment/cancel/' . Hashids::connection('sale_id')
                                                                                                 ->encode($sale->id);
            } else {
                $urlCancelPayment = 'http://' . env('CHECKOUT_URL') . '/api/payment/cancel/' . Hashids::connection('sale_id')
                                                                                                      ->encode($sale->id);
            }
            $dataCancel = [
                'refundAmount' => (!empty($partialValues['value_to_refund'])) ? $partialValues['value_to_refund'] : $refundAmount,
                'partial'      => (!empty($partialValues)) ? true : false,
            ];
            $response   = $this->runCurl($urlCancelPayment, 'POST', $dataCancel);
            if (($response->status ?? '') == 'success') {
                $checkUpdate = $saleService->updateSaleRefunded($sale, $refundAmount, $response, $partialValues);
                if ($checkUpdate) {
                    $userCompanies = $companyModel->where('user_id', $sale->owner_id)->pluck('id');
                    $transaction   = $transactionModel->where('sale_id', $sale->id)
                                                      ->whereIn('company_id', $userCompanies)
                                                      ->first();
                    $transferModel->create([
                                               'transaction_id' => $transaction->id,
                                               'user_id'        => auth()->user()->account_owner_id,
                                               'value'          => 100,
                                               'type_enum'      => $transferModel->present()->getTypeEnum('out'),
                                               'type'           => 'out',
                                               'reason'         => 'Taxa de estorno',
                                               'company_id'     => $transaction->company_id,
                                           ]);
                    $transaction->company->update([
                                                      'balance' => $transaction->company->balance -= 100,
                                                  ]);
                    $result = [
                        'status'  => 'success',
                        'message' => 'Venda Estornada com sucesso.',
                    ];
                } else {
                    $result = [
                        'status'  => 'error',
                        'message' => 'Venda Estornada, mas não atualizada na plataforma.',
                    ];
                }
            } else {
                $result = [
                    'status'  => 'error',
                    'message' => 'Error ao tentar cancelar venda.',
                    'error'   => $response->message,
                ];
            }

            return $result;
        } catch (Exception $ex) {
            report($ex);

            return [
                'status'  => 'error',
                'message' => 'Error ao tentar cancelar venda.',
                'error'   => $ex->getMessage(),
            ];
        }
    }

    /**
     * @param $saleId
     * @param $totalPaidValue
     * @param $dueDate
     * @return array
     */
    public function regenerateBillet($saleId, $totalPaidValue, $dueDate)
    {

        try {
            $saleIdDecode = current(Hashids::connection('sale_id')->decode($saleId));
            $saleModel = new Sale();
            $sale      = $saleModel::with('project.domains')->where('id', $saleIdDecode)->first();

            $billets = RegeneratedBillet::where('sale_id', $saleIdDecode)
                                        ->orWhere('owner_id', $sale->owner_id)
                                        ->limit(6)
                                        ->get();

            if($billets->where('sale_id', $saleIdDecode)->count() > 1) {
                return [
                    'status'   => 'error',
                    'error'    => 'error',
                    'message'  => 'Só é permitido regerar 4 boletos por venda',
                ];
            }

            if($billets->where('created_at', '>', Carbon::now()->subMinute())->count() > 1) {
                return [
                    'status'   => 'error',
                    'error'    => 'error',
                    'message'  => 'Aguarde um instante, só é permitido regerar 2 boletos por minuto',
                ];
            }


            $domain    = $sale->project->domains->where('status', 3)->first();
            if (FoxUtils::isProduction()) {
                $regenerateBilletUrl = 'https://checkout.cloudfox.net/api/payment/regeneratebillet';
            } else {
                $regenerateBilletUrl = env('CHECKOUT_URL', 'http://dev.checkout.com.br') . '/api/payment/regeneratebillet';
            }

            $data = [
                'sale_id'          => $saleId,
                'due_date'         => $dueDate,
                'total_paid_value' => $totalPaidValue,
            ];

            $response = $this->runCurl($regenerateBilletUrl, 'POST', $data);
            if ($response->status == 'success' && $response->response->status == 'success') {
                $saleModel  = new Sale();
                $dataUpdate = (array) $response->response->response;
                if (!empty($dataUpdate['gateway_received_date'])) {
                    unset($dataUpdate['gateway_received_date']);
                }
                $check = $saleModel->where('id', $saleIdDecode)
                                   ->update(array_merge($dataUpdate,
                                                        [
                                                            'start_date'       => Carbon::now(),
                                                            'total_paid_value' => substr_replace($totalPaidValue, '.', strlen($totalPaidValue) - 2, 0),
                                                        ]));
                if ($check) {
                    RegeneratedBillet::create([
                        'sale_id'                      => $saleIdDecode,
                        'billet_link'                  => $dataUpdate['boleto_link'],
                        'billet_digitable_line'        => $dataUpdate['boleto_digitable_line'],
                        'billet_due_date'              => $dataUpdate['boleto_due_date'],
                        'gateway_transaction_id'       => $dataUpdate['gateway_transaction_id'],
                        'gateway_billet_identificator' => $dataUpdate['gateway_billet_identificator'] ?? null,
                        'gateway_id'                   => $dataUpdate['gateway_id'],
                        'owner_id'                     => $sale->owner_id,
                    ]);

                    $transactionModel = new Transaction();
                    $sale             = $saleModel::with('project.domains')
                                                  ->where('id', $saleIdDecode)
                                                  ->first();
                    $transactionModel->where('sale_id', $saleIdDecode)->delete();

                    $splitPaymentService = new SplitPaymentService();

                    $splitPaymentService->splitPayment($totalPaidValue, $sale, $sale->project, $sale->user);
                    $result = [
                        'status'   => 'success',
                        'message'  => print_r($response->message, true) ?? '',
                        'response' => $response,
                    ];
                } else {
                    $result = [
                        'status'   => 'error',
                        'error'    => 'error',
                        'message'  => 'Error ao tentar regerar boleto, tente novamente em instantes!',
                        'response' => $response,
                    ];
                }
            } else {
                $result = [
                    'status'   => 'error',
                    'error'    => 'error',
                    'message'  => 'Error ao tentar regerar boleto, tente novamente em instantes!',
                    'response' => $response,
                ];
            }

            return $result;
        } catch (Exception $ex) {
            report($ex);

            return [
                'status'  => 'error',
                'message' => 'Error ao tentar regerar boleto.',
                'error'   => $ex->getMessage(),
            ];
        }
    }

    /**
     * @param $url
     * @param string $method
     * @param null $data
     * @return mixed
     * @throws Exception
     * @description GET/POST/PUT/DELETE
     */
    public function runCurl($url, $method = 'GET', $data = null)
    {
        try {
            $this->internalApiToken = env('ADMIN_TOKEN');
            $headers                = [
                'Content-Type: application/json',
                'Accpet: application/json',
            ];
            if (!empty($this->internalApiToken)) {
                $headers[] = 'Api-name:ADMIN';
                $headers[] = 'Api-token:' . $this->internalApiToken;
            }
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            if ($method == "POST") {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result   = curl_exec($ch);
            $response = json_decode($result);

            return $response;
        } catch (Exception $ex) {
            report($ex);
            throw $ex;
        }
    }

    public function verifyCheckoutStatus()
    {

        $cloudFlareService = new CloudFlareService();

        if (!$cloudFlareService->checkHtmlMetadata('https://checkout.cloudfox.net/', 'checkout-cloudfox', '1')) {

            // checkout OFF

            // email addresses for notify
            $emails = [
                'julioleichtweis@gmail.com',
                'felixlorram@gmail.com',
            ];

            // phone numbers for notify
            $phoneNumbers = [
                '5555996931098',
                '5522981071202',
            ];

            $sendgrid   = new SendGrid(getenv('SENDGRID_API_KEY'));
            $smsService = new SmsService();

            foreach ($emails as $email) {

                try {
                    $sendgridMail = new Mail();
                    $sendgridMail->setFrom('noreply@cloudfox.net', 'cloudfox');
                    $sendgridMail->addTo($email, 'cloudfox');
                    $sendgridMail->setTemplateId('d-f44033c3eaec46d2a6226f796313d9fc');

                    $response = $sendgrid->send($sendgridMail);
                } catch (Exception $e) {
                    //
                }
            }

            foreach ($phoneNumbers as $phoneNumber) {

                try {
                    $smsService->sendSms($phoneNumber, 'Checkout caiu');
                } catch (Exception $e) {
                    //
                }
            }

            return true;
        }
    }
}
