<?php

namespace Modules\PostBack\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Core\Entities\BraspagBackofficePostback;

class PostBackBraspagOfficeController extends Controller
{
    private $braspagBackofficePostback;

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
        } catch (Exception $e) {
            report($e);
        }
    }
}
