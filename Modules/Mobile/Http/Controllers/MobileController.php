<?php

namespace Modules\Mobile\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Transaction;
use Modules\Mobile\Transformers\SalesResource;

class MobileController extends Controller
{
    /**
     * Returns the sales of the user's company.
     *
     * @return JsonResponse
     */
    public function sales(Request $request)
    {
        try {
            $companyId = hashids_decode($request->company_id);

            $relations = [
                'sale',
                'sale.project',
                'sale.plansSales',
                'sale.productsPlansSale.plan',
                'sale.productsPlansSale.product'
            ];

            $sales = Transaction::with($relations)
                ->selectRaw('transactions.*')
                ->join('sales', 'sales.id', 'transactions.sale_id')
                ->whereNull('invitation_id')
                ->where('type', Transaction::TYPE_PRODUCER)
                ->where('company_id', $companyId)
                ->orderByDesc('sales.start_date');

            if($request->has('limit')) {
                $sales->limit($request->limit);
            }

            return SalesResource::collection($sales->get());
        }
         catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao carregar vendas'], 400);
        }
    }
}
