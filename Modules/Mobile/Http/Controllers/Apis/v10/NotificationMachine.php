<?php

namespace Modules\Mobile\Http\Controllers\Apis\v10;

use App\Entities\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\SaleService;
use Modules\Sales\Transformers\SalesResource;
use Vinkla\Hashids\Facades\Hashids;

class NotificationMachine {

    //https://www.lucidchart.com/invitations/accept/a3fe223a-ab45-4f4c-9a61-7d660b49b1ab
    /**
     * @var array
     */
    private $state = [
        1  => 'init',
        20 => 'finish',
        2  => 'validateRequest',
        3  => 'checkSaleNotification',
        4  => 'ignorePostback',
        5  => 'checkWithdrawalsNotification',
        6  => 'findSale',
    ];

    /**
     * @var array
     */
    private $status = [
        1 => 'success',
        2 => 'error',
        3 => 'exception',
    ];

    /**
     * @var array
     */
    private $result = [
        'history'     => [],
        'data'        => null,
        'state_error' => null,
        'exception'   => null,
        'status'      => 1, // 1 success, 2 - error
        'state'       => 1,
    ];

    /**
     * @var integer
     */
    private $actualState;
    /**
     * @var
     */
    private $postbackJson;
    /**
     * @var Request
     */
    private $postback;
    /**
     * @var NotificationApiService
     */
    private $notificationService;
    /**
     * @var
     */
    private $foxSale;

    /**
     * @param $state
     */
    private function setState($state)
    {
        $this->actualState     = array_search($state, $this->state);
        $this->result['state'] = array_search($state, $this->state);
        array_push($this->result['history'], $this->actualState);
    }

    /**
     * @param $state
     * @param null $message
     * @return array
     */
    private function failState($state, $message = null)
    {
        $this->result['state_error'] = array_search($state, $this->state);
        $this->result['exception']   = $message;
        $this->result['status']      = 2;

        return $this->finish();
    }

    /**
     * @param Request $postback
     * @return array
     */
    public function init(Request $postback)
    {
        try {
            $this->setState(__FUNCTION__);

            //$this->$notificationService = new NotificationApiService();

            $this->result['status'] = 1;

            $this->postback     = $postback;
            $this->postbackJson = json_decode($postback->getContent());

            $this->foxSale      = null;

            return $this->validateRequest();
        } catch (Exception $ex) {
            Log::warning('NotificaionMachine - ' . __FUNCTION__);
            report($ex);

            return $this->failState(__FUNCTION__, $ex->getMessage());
        }
    }

    /**
     * @return array
     */
    public function validateRequest()
    {
        try {
            $this->setState(__FUNCTION__);

            if (!empty($this->postbackJson->notification_type) && !empty($this->postbackJson->external_reference)) {
                return $this->checkSaleNotification();
            } else {
                return $this->ignorePostback();
            }
        } catch (Exception $ex) {
            Log::warning('NotificaionMachine - ' . __FUNCTION__);
            report($ex);

            return $this->failState(__FUNCTION__, $ex->getMessage());
        }
    }

    /**
     * @return array
     */
    public function checkSaleNotification()
    {
        try {
            $this->setState(__FUNCTION__);

            if ($this->postbackJson->notification_type == 'sale') {
                return $this->findSale();
            } else {
                //return $this->checkWithdrawalsNotification();
            }
        } catch (Exception $ex) {
            Log::warning('NotificaionMachine - ' . __FUNCTION__);
            report($ex);

            return $this->failState(__FUNCTION__, $ex->getMessage());
        }
    }


    /**
     * @return array
     */
    public function checkWithdrawalsNotification()
    {
        try {
            $this->setState(__FUNCTION__);

            if ($this->postbackJson->notification_type == 'withdrawals') {
                return $this->checkEventConfirmed();
            } else {
                return $this->ignorePostback();
            }
        } catch (Exception $ex) {
            Log::warning('NotificaionMachine - ' . __FUNCTION__);
            report($ex);

            return $this->failState(__FUNCTION__, $ex->getMessage());
        }
    }


    /**
     * @return array
     */
    public function findSale()
    {
        try {
            $this->setState(__FUNCTION__);

            $saleId = current(Hashids::connection('sale_id')->decode($this->postbackJson->external_reference));

            $saleService = new SaleService();

            if (isset($this->postbackJson->external_reference)) {
                $sale         = $saleService->getSaleWithDetails($this->postbackJson->external_reference);
                $this->foxSale = new SalesResource($sale);

                if ($this->foxSale) {

                }
            }

            $saleModel = new Sale();

            $saleId = current(Hashids::connection('sale_id')->decode($this->postbackJson->externalReference));

            if ($saleId) {
                //$this->foxSale = $saleModel->with(['transactions.company.user', 'client'])->find($saleId);
                $this->foxSale = $saleModel->find($saleId);

                if ($this->foxSale) {

                }
            }

            if ($this->postbackJson->external_reference == 'withdrawals') {
                return $this->checkEventConfirmed();
            } else {
                return $this->ignorePostback();
            }
        } catch (Exception $ex) {
            Log::warning('NotificaionMachine - ' . __FUNCTION__);
            report($ex);

            return $this->failState(__FUNCTION__, $ex->getMessage());
        }
    }


    /**
     * @return array
     */
    public function ignorePostback()
    {
        try {
            $this->setState(__FUNCTION__);

            $this->postback->update([

                'processed_flag'      => true,
                'postback_valid_flag' => true,
            ]);

            return $this->finish();
        } catch (Exception $ex) {
            Log::warning('NotificaionMachine - ' . __FUNCTION__);
            report($ex);

            return $this->failState(__FUNCTION__, $ex->getMessage());
        }
    }

    /**
     * @return array
     */
    private function finish()
    {
        try {
            $this->setState(__FUNCTION__);

            $returnData = [
                'result' => $this->result,
                'state'  => $this->actualState,
            ];

            $this->postback->update([
                'machine_result' => json_encode($returnData),
                'sale_id' => Hashids::connection('sale_id')->decode($this->postbackJson->payment->externalReference)[0],
                'reference_id' => $this->postbackJson->payment->externalReference
            ]);

            return $returnData;
        } catch (Exception $ex) {
            Log::warning('NotificaionMachine - ' . __FUNCTION__);
            report($ex);

            return $this->failState(__FUNCTION__, $ex->getMessage());
        }
    }
}
