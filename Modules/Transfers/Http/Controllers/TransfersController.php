<?php

namespace Modules\Transfers\Http\Controllers;

use App\Entities\Transfer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Transfers\Transformers\TransfersResource;

class TransfersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            $transfer = new Transfer();

            $transfersModel = new Transfer();

            $transfers = $transfersModel
                        ->select('transfers.*','transaction.sale','transaction.company','transaction.currency')
                        ->leftJoin('transactions as transaction','transaction.id','transfers.transaction')
                        ->where('transaction.company',current(Hashids::decode($request->company)));

            return TransfersResource::collection($transfers->paginate(10));
        } catch (Exception $e) {
            Log::warning('Erro ao buscar lista de transferencias (TransfersController - index)');
            report($e);
        }
    }
}
