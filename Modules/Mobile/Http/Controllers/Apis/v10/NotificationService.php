<?php

namespace Modules\Mobile\Http\Controllers\Apis\v10;

use Illuminate\Http\Request;

class NotificationService {

    public function __construct()
    {

    }

    public function processPostback(Request $request)
    {
        try {
            $notificationMachine = new NotificationMachine();
            $notificationMachine->init($request);
        } catch (Exception $ex) {
            $this->exceptions[] = $ex->getMessage();
            throw $ex;
        }
    }
}
