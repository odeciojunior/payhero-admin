<?php

namespace Modules\Api\Http\Controllers\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Services\Api\SaleApiService;
use Modules\Sales\Transformers\TransactionResource;

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

        try {

            $saleService = new SaleApiService();

            $sales = $saleService->getPaginatedSales($request->all());

            return TransactionResource::collection($sales);

        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao carregar vendas"], 400);
        }
    }
}
