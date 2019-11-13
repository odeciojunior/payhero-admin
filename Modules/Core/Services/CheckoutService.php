<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Presenters\SalePresenter;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class CheckoutService
 * @package Modules\Core\Services
 */
class CheckoutService
{
    /**
     * @var string
     */
    private $urlCancelPayment = 'http://checkout.devcloudfox.net/api/payment/cancel/';
    /**
     * @var
     */
    private $internalApiToken;

    /**
     * @param string|null $projectId
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @param string|null $client
     * @return AnonymousResourceCollection
     */
    public function getAbandonedCart(string $projectId = null, string $dateStart = null, string $dateEnd = null, string $client = null)
    {
        $checkoutModel = new Checkout();
        $domainModel   = new Domain();

        $abandonedCarts = $checkoutModel->whereIn('status', ['recovered', 'abandoned cart'])
                                        ->where('project_id', $projectId);

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
            $total += intval(preg_replace("/[^0-9]/", "", $checkoutPlan->plan->price)) * intval($checkoutPlan->amount);
        }

        return $total;
    }

    /*
        public function getProducts()
        {
            $products = [];
            foreach ($this->checkoutPlans as $checkoutPlan) {
                foreach ($checkoutPlan->plan()->first()->productsPlans as $productPlan) {
                    $product           = $productPlan->product()->first()->toArray();
                    $product['amount'] = $productPlan->amount * $checkoutPlan->amount;
                    $products[]        = $product;
                }
            }

            return $products;
        }*/

    public function cancelPayment($sale, $refundAmount)
    {
        try {
            $saleService      = new SaleService();
            $transactionModel = new Transaction();
            $transferModel    = new Transfer();
            $companyModel     = new Company();
            $saleAmount       = Str::replaceFirst(',', '', Str::replaceFirst('.', '', Str::replaceFirst('R$ ', '', $sale->total_paid_value)));
            // TODO não estamos implementando devolução parcial, quando for implementar tirar '|| $refundAmount < $saleAmount'
            if ($refundAmount > $saleAmount || $refundAmount < $saleAmount) {
                $result = [
                    'status'  => 'error',
                    'message' => 'Valor não confere com o da Venda.',
                ];
            }
            $urlCancelPayment = $this->urlCancelPayment . Hashids::connection('sale_id')->encode($sale->id);
            $dataCancel       = [
                'refundeAmount' => $refundAmount,
            ];
            $response         = $this->runCurl($urlCancelPayment, 'POST', $dataCancel);
            if ($response->status == 'success') {
                $checkUpdate = $saleService->updateSaleRefunded($sale, $refundAmount, $response);
                if ($checkUpdate) {
                    $userCompanies = $companyModel->where('user_id', auth()->user()->account_owner_id)->pluck('id');
                    $transaction   = $transactionModel->where('sale_id', $sale->id)
                                                      ->whereIn('company_id', $userCompanies)
                                                      ->first();
                    $transferModel->create([
                                               'transaction_id' => $transaction->id,
                                               'user_id'        => auth()->user()->account_owner_id,
                                               'value'          => 100,
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
            return [
                'status'  => 'error',
                'message' => 'Error ao tentar cancelar venda.',
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
            throw $ex;
        }
    }
}
