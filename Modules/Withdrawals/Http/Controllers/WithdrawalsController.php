<?php

namespace Modules\Withdrawals\Http\Controllers;

use App\Entities\Company;
use App\Entities\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Services\BankService;
use Modules\Withdrawals\Transformers\WithdrawalResource;

class WithdrawalsController extends Controller {


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

    public function store(Request $request){

        return response()->json($request->all());

    }

    public function getAccountInformation($companyId){

        $companyModel = new Company();
        $bankService  = new BankService();

        $company = $companyModel->find(current(Hashids::decode($companyId)));

        return response()->json([
            'message' => 'success',
            'data' => [
                'bank'          => $bankService->getBankName($company->bank),
                'account'       => $company->account,
                'account_digit' => $company->account_digit,
                'agency'        => $company->agency,
                'agency_digit'  => $company->agency_digit,
                'document'      => $company->company_document,
            ]
        ],200);
    }

}


