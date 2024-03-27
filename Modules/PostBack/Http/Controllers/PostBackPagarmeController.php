<?php

namespace Modules\PostBack\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\PostbackLog;

class PostBackPagarmeController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function postBackListener(Request $request)
    {
        $requestData = $request->all();

        $postBackLogModel = new PostbackLog();

        $postBackLogModel->create([
            "origin" => 2,
            "data" => json_encode($requestData),
            "description" => "pagarme",
        ]);

        return response()->json(["message" => "success"], 200);
    }
}
