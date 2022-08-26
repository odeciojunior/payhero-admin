<?php

namespace Modules\Api\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class SalesApiController extends Controller
{

    public function index(Request $request)
    {
        $rules = [
            "project" => "nullable|string",
            "transaction" => "nullable",
            "payment_method" => "nullable|string",
            "status" => "nullable",
            "client" => "nullable|string",
            "date_type" => "required",
            "date_range" => "required",
        ];

        $message =[
            "date_type.required" => "O campo data e obrigatório",
            "date_range.required" => "É preciso selecionar um período",
        ];

        $validator = Validator::make ($request->all(),$rules,$message);

        if ($validator->fails()) {
            return ['status'=>'error','message'=>$validator->errors()->all()];
        }

        return ['ok'=>true];
    }
}
