<?php

namespace Modules\Mobile\Http\Controllers\Apis\v10;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\PushNotification;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\SaleService;
use Vinkla\Hashids\Facades\Hashids;

class NotificationMachine
{
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
        7  => 'getUserDevices',
        8  => 'makeNotification',
        9  => 'sendNotification',
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
     * @var PushNotification
     */
    private $pushNotification;
    /**
     * @var integer
     */
    private $actualState;
    /**
     * @var
     */
    private $requestJson;
    /**
     * @var NotificationApiService
     */
    private $notificationService;
    /**
     * @var
     */
    private $foxSale;
    /**
     * @var array
     */
    private $oneSignalResponse;

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
     * @param PushNotification $pushNotification
     * @return array
     */
    public function init(PushNotification $pushNotification)
    {
        try {
            $this->setState(__FUNCTION__);

            $this->notificationService = new NotificationApiService();

            $this->result['status'] = 1;
            $this->pushNotification = $pushNotification;
            $this->foxSale          = null;
            $this->requestJson      = json_decode($pushNotification->postback_data);

            //$code = Hashids::connection('sale_id')->encode($this->requestJson->sale_id);

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

            if (!empty($this->requestJson->notification_type) && !empty($this->requestJson->external_reference)) {
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

            if ($this->requestJson->notification_type == 'sale') {
                return $this->findSale();
            } else {
                return $this->checkWithdrawalsNotification();
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

            if ($this->requestJson->notification_type == 'withdrawals') {
                //return $this->checkEventConfirmed();
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
            $saleModel = new Sale();

            if (isset($this->requestJson->external_reference)) {
                $saleId        = current(Hashids::connection('sale_id')
                                                ->decode($this->requestJson->external_reference));
                $this->foxSale = $saleModel->with(['transactions.company.user.userDevices', 'client', 'plansSales.plan'])
                                           ->find($saleId);
                if ($this->foxSale) {
                    return $this->getUserDevices();
                }
            }

            return $this->failState(__FUNCTION__, 'NotificaionMachine [findSale] - Venda não encontrada no banco de dados!');
        } catch (Exception $ex) {
            Log::warning('NotificaionMachine - ' . __FUNCTION__);
            report($ex);

            return $this->failState(__FUNCTION__, $ex->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getUserDevices()
    {
        try {
            $this->setState(__FUNCTION__);

            $userDevices = [];
            foreach ($this->foxSale->transactions as $transaction) {
                if (isset($transaction->company->user->userDevices)) {
                    if ($transaction->type == 2 || $transaction->type == 3) { // 2 - venda normal / 3 - indicação
                        foreach ($transaction->company->user->userDevices as $device) {
                            if ($device->online) { // verifica se o device está ativo
                                if ($transaction->type == 3 && $device->invitation_sale_notification) { // notificação de indicação
                                    $userDevices[] = [
                                        'player_id'   => $device->player_id,
                                        'value'       => $transaction->value,
                                        'type'        => 3,
                                        'device_type' => $device->device_type,
                                    ];
                                } else if ($transaction->type == 2 && $this->foxSale->payment_method == 1) { // venda
                                    if ($device->sale_notification) { // verifica se o usuário quer ser notificado na venda
                                        $userDevices[] = [
                                            'player_id'   => $device->player_id,
                                            'value'       => $transaction->value,
                                            'type'        => 2,
                                            'device_type' => $device->device_type,
                                        ];
                                    }
                                } else if ($transaction->type == 2 && $this->foxSale->payment_method == 2) { // boleto
                                    if ($device->billet_notification) { // verifica se o usuário quer ser notificado no boleto
                                        $userDevices[] = [
                                            'player_id'   => $device->player_id,
                                            'value'       => $transaction->value,
                                            'type'        => 2,
                                            'device_type' => $device->device_type,
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (sizeof($userDevices) == 0) {
                return $this->ignorePostback();
            } else {
                return $this->makeNotification($userDevices);
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
    public function makeNotification($userDevices)
    {
        try {
            $this->setState(__FUNCTION__);

            $heading       = '';
            $sound         = '';
            $saleService   = new SaleService();
            $products      = $saleService->getProducts($this->foxSale->id);
            $notifications = [];

            foreach ($userDevices as $device) {

                $content = $products[0]->name . " - R$ " . number_format(intval($device['value']) / 100, 2, ',', '.');

                // venda cartão
                if ($this->foxSale->payment_method == 1) {
                    $heading = 'CloudFox - Venda realizada';
                    $sound   = 'venda';
                } else { // boleto
                    if ($this->foxSale->status == 1) { // boleto pago
                        $heading = 'CloudFox - Boleto pago';
                        $sound   = 'boleto';
                    } else { // boleto gerado
                        $heading = 'CloudFox - Boleto gerado';
                        $sound   = 'boleto';
                    }
                }

                $notifications[] = [
                    "headings"           => $device['type'] == 3 ? $heading . ' (indicação)' : $heading,
                    "content"            => $content,
                    "notification_sound" => $sound,
                    "include_player_ids" => [$device['player_id']],
                    "device_type"        => $device['device_type'],
                ];
            }

            return $this->sendNotification($notifications);
        } catch (Exception $ex) {
            Log::warning('NotificaionMachine - ' . __FUNCTION__);
            report($ex);

            return $this->failState(__FUNCTION__, $ex->getMessage());
        }
    }

    /**
     * @return array
     */
    public function sendNotification($notifications)
    {
        try {
            $this->setState(__FUNCTION__);

            foreach ($notifications as $notification) {
                $this->oneSignalResponse = $this->notificationService->sendNotification($notification);

                if ($this->oneSignalResponse["status"] != 200) {
                    return $this->failState(__FUNCTION__, $this->oneSignalResponse["response"]);
                }
            }

            return $this->ignorePostback();
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

            $this->pushNotification->update([
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

            $this->pushNotification->update([
                                                'machine_result'     => json_encode($returnData),
                                                'onesignal_response' => $this->oneSignalResponse["response"] ?? null,
                                            ]);

            return $returnData;
        } catch (Exception $ex) {
            Log::warning('NotificaionMachine - ' . __FUNCTION__);
            report($ex);

            return $this->failState(__FUNCTION__, $ex->getMessage());
        }
    }
}
