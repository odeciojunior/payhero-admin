<?php

namespace Modules\Api\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class SubsellersApiController extends Controller
{
    public function create()
    {
        try {
            return response()->json([
                'data' => ''
            ], Response::HTTP_OK);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => ''
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
