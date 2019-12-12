<?php

namespace Modules\Mobile\Http\Controllers\Apis\v10;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationMachine {

    //https://www.lucidchart.com/invitations/accept/be79134f-e172-447b-b568-b6a38d6dab9e
    /**
     * @var array
     */
    private $state = [
        1  => 'init',
        20 => 'finish',
        2  => 'validateRequest',

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
     * @var NotificationService
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

            //$this->$notificationService = new NotificationService();

            $this->result['status'] = 1;

            $this->postback     = $postback;
            $this->postbackJson = json_decode($postback->data);

            $this->foxSale      = null;

            return $this->requestValidate();
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

            if (!empty($this->postbackJson->event) && !empty($this->postbackJson->payment)) {
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


}
