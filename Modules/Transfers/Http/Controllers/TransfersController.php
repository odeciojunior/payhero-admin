<?php

namespace Modules\Transfers\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Transfer;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Transfers\Transformers\TransfersResource;

class TransfersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $transfersModel = new Transfer();

            $transfers = $transfersModel
                ->select('transfers.*', 'transaction.sale_id', 'transaction.company_id', 'transaction.currency', 'transaction.status',
                         'transaction.antecipable_value', 'anticipatedtransaction.anticipation_id', 'anticipatedtransaction.created_at as anticipationCreatedAt')
                ->leftJoin('transactions as transaction', 'transaction.id', 'transfers.transaction_id')
                ->leftJoin('anticipated_transactions as anticipatedtransaction', 'anticipatedtransaction.transaction_id', 'transfers.transaction_id')
                ->where('transfers.company_id', current(Hashids::decode($request->company)))
                ->orWhere('transaction.company_id', current(Hashids::decode($request->company)))
                ->orderBy('id', 'DESC');

            return TransfersResource::collection($transfers->paginate(10));
        } catch (Exception $e) {
            Log::warning('Erro ao buscar lista de transferencias (TransfersController - index)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                    ], 400);
        }
    }
}
