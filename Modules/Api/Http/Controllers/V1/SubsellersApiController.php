<?php

namespace Modules\Api\Http\Controllers\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class SubsellersApiController extends Controller
{
    public function createSubseller(Request $request)
    {
        try {
            return response()->json([
                'data' => $request
            ], Response::HTTP_OK);
        } catch(Exception $exception) {
            report($exception);

            return response()->json([
                'message' => ''
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showSubseller($id)
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

    public function updateSubseller($id)
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

    public function sendDocumentsSubseller($id, Request $request)
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
