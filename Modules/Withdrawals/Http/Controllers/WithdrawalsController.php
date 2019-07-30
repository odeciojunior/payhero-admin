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
        if ($withdrawalValue > $company->balance) {

            return response()->json([
                                        'message' => 'Valor informado inválido',
                                        'data'    => [
                                            'status' => 'error',
                                        ],
                                    ], 400);
        }

        $withdrawalModel->create([
                                     'user'                => auth()->user()->id,
                                     'value'               => $withdrawalValue,
                                     'company_id'          => $company->id,
                                     'account_information' => $bankService->getBankName($company->bank) . ' - Agência: ' . $company->agency .' - Digito: '. $company->agency_digit.' - Conta: ' . $company->account.' - Digito: ' .$company->account_digit,
                                     'status'              => $companyModel->getEnum('bank_document_status', 'pending'),
                                 ]);

        return response()->json([
                                    'message' => 'Saque pendente',
                                    'data'    => [
                                        'status' => 'success',
                                    ],
                                ]);
    }

    public function getAccountInformation($companyId)
    {

        $companyModel = new Company();
        $bankService  = new BankService();

        $company = $companyModel->find(current(Hashids::decode($companyId)));

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


