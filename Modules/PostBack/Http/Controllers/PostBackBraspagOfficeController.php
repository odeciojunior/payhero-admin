<?php

namespace Modules\PostBack\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Core\Entities\BraspagBackofficePostback;
use Modules\Core\Events\ProcessPostbackBraspagBackOfficeEvent;
use Modules\Core\Services\BraspagBackOfficeService;

class PostBackBraspagOfficeController extends Controller
{
    private BraspagBackofficePostback $braspagBackofficePostback;

    public function __construct(BraspagBackofficePostback $braspagBackofficePostback)
    {
        $this->braspagBackofficePostback = $braspagBackofficePostback;
    }

    public function postBackBraspagOffice(Request $request)
    {
        try {
            $requestData = $request->all();

            $this->braspagBackofficePostback->create([
                'data' => json_encode($requestData)
            ]);

            if (!empty($requestData['MerchantId']) && !empty($requestData['Status'])) {
                event(new ProcessPostbackBraspagBackOfficeEvent($requestData['MerchantId'], $requestData['Status']));
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
