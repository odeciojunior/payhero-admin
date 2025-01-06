<?php

namespace Modules\PostBack\Http\Controllers;

use App\Jobs\ProcessNuvemshopPostbackJob;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PostBackNuvemshopController extends Controller
{
    public function postBackListener(Request $request)
    {
        try {
            $requestData = $request->all();
            $projectId = hashids_decode($request->project_id);

            ProcessNuvemshopPostbackJob::dispatch($projectId, $requestData);

            return response()->json(["message" => "success"]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "error processing postback"]);
        }
    }
}
