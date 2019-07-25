<?php

namespace Modules\Withdrawals\Http\Controllers;

use App\Entities\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Withdrawals\Transformers\WithdrawalResource;

class WithdrawalsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request) {

        try {
            $withdrasalModel = new Withdrawal();

            $withdrawals = $withdrasalModel
                ->where('company_id', current(Hashids::decode($request->company_id)))->orderBy('id', 'DESC');

            return WithdrawalResource::collection($withdrawals->paginate(10));
        } catch (Exception $e) {
            Log::warning('Erro ao buscar lista de saques (WithdrawalsController - index)');
            report($e);
        }

    }

}
