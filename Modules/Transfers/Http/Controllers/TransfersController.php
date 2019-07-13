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
    private $transfer;

    private function getTransfer()
    {
        if (!$this->transfer) {
            $this->transfer = app(Transfer::class);
        }

        return $this->transfer;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request) {
        try {

            $transfers = $this->getTransfer()
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
