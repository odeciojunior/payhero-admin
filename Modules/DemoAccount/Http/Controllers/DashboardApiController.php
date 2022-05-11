<?php

namespace Modules\DemoAccount\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller{
    public function getValues(Request $request): JsonResponse
    {
        dd($request->all());
        return response()->json(
            [
                'message' => 'Ocorreu um erro, tente novamente mais tarde',
            ],
            400
        );
    }
}