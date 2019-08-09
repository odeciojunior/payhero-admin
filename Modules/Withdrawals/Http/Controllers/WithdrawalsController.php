<?php

namespace Modules\Withdrawals\Http\Controllers;

use App\Entities\Company;
use App\Entities\User;
use App\Entities\Withdrawal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Services\BankService;
use Modules\Withdrawals\Transformers\WithdrawalResource;

class WithdrawalsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $withdrawalModel = new Withdrawal();

            $withdrawals = $withdrawalModel
                ->where('company_id', current(Hashids::decode($request->company)))->orderBy('id', 'DESC');

            return WithdrawalResource::collection($withdrawals->paginate(5));
        } catch (Exception $e) {
            Log::warning('Erro ao buscar lista de saques (WithdrawalsController - index)');
            report($e);
        }
    }

    public function store(Request $request)
    {

        $data = $request->all();

        $withdrawalModel = new Withdrawal();
        $companyModel    = new Company();
        $bankService     = new BankService();

        $company = $companyModel->find(current(Hashids::decode($data['company_id'])));
        if (!$company->bank_document_status == $companyModel->getEnum('bank_document_status', 'approved') ||
            !$company->address_document_status == $companyModel->getEnum('address_document_status', 'approved') ||
            !$company->contract_document_status == $companyModel->getEnum('contract_document_status', 'approved')) {
            return response()->json([
                                        'message' => 'error',
                                        'data'    => [
                                            'documents_status' => 'pending',
                                        ],
                                    ], 400);
        }

        $withdrawalValue = preg_replace("/[^0-9]/", "", $data['withdrawal_value']);
        if ($withdrawalValue < 1000) {
            return response()->json([
                                        'message' => 'Valor de saque precisa ser maior que R$ 10,00',
                                    ], 400);
        }

        if ($withdrawalValue > $company->balance) {

            return response()->json([
                                        'message' => 'Valor informado inválido',
                                    ], 400);
        }

        $company->update([
                             'balance' => $company->balance -= $withdrawalValue,
                         ]);
        $withdrawalValue -= 380;
        $withdrawalModel->create([
                                     'user'          => auth()->user()->id,
                                     'value'         => $withdrawalValue,
                                     'company_id'    => $company->id,
                                     'bank'          => $company->bank,
                                     'agency'        => $company->agency,
                                     'agency_digit'  => $company->agency_digit,
                                     'account'       => $company->account,
                                     'account_digit' => $company->account_digit,
                                     'status'        => $companyModel->getEnum('bank_document_status', 'pending'),
                                 ]);

        return response()->json([
                                    'message' => 'Saque pendente',
                                ], 200);
    }

    public function getAccountInformation($companyId)
    {

        $companyModel = new Company();
        $bankService  = new BankService();
        $userModel    = new User();
        $company      = $companyModel->find(current(Hashids::decode($companyId)));
        $user         = $userModel->where('id', auth()->user()->id)->first();
        if ($user->address_document_status != $userModel->getEnum('address_document_status', 'approved') ||
            $user->personal_document_status != $userModel->getEnum('personal_document_status', 'approved')) {

            return response()->json([
                                        'message' => 'success',
                                        'data'    => [
                                            'user_documents_status' => 'pending',
                                        ],
                                    ], 200);
        }

        if ($company->bank_document_status == $companyModel->getEnum('bank_document_status', 'approved') &&
            $company->address_document_status == $companyModel->getEnum('address_document_status', 'approved') &&
            $company->contract_document_status == $companyModel->getEnum('contract_document_status', 'approved')) {
            return response()->json([
                                        'message' => 'success',
                                        'data'    => [
                                            'documents_status' => 'approved',
                                            'bank'             => $bankService->getBankName($company->bank),
                                            'account'          => $company->account,
                                            'account_digit'    => $company->account_digit,
                                            'agency'           => $company->agency,
                                            'agency_digit'     => $company->agency_digit,
                                            'document'         => $company->company_document,
                                        ],
                                    ], 200);
        } else {
            return response()->json([
                                        'message' => 'success',
                                        'data'    => [
                                            'documents_status' => 'pending',
                                        ],
                                    ], 200);
        }
    }
}


