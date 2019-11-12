<?php


namespace Modules\Mobile\Http\Controllers\Apis\v10;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Core\Services\BankService;
use Modules\Transfers\Transformers\TransfersResource;
use Modules\Withdrawals\Transformers\WithdrawalResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class FinanceApiService
 * @package Modules\Mobile\Http\Controllers\Apis\v10
 */
class FinanceApiService {

    /**
     * FinanceApiService constructor.
     */
    public function __construct() {  }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function financeGetData(Request $request) {

        try {

            $balances = $this->getBalances($request);
            $transactions = $this->getTransactions($request);
            $bankAccount = $this->getAccountInformation($request);

            return response()->json(compact('balances', 'transactions', 'bankAccount'), 200);

        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados da dashboard (FinanceApiService - financeGetData)');
            report($e);

            return response()->json([
                'message' => 'Ocorreu um erro, tente novamente mais tarde',
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getTransactions(Request $request) {

        try {
            /** @var Withdrawal $withdrawalModel */
            $withdrawalModel = new Withdrawal();
            /** @var Company $companyModel */
            $companyModel = new Company();
            $companyId    = current(Hashids::decode($request->company));
            if ($companyId) {
                //id existe
                $company = $companyModel->find($companyId);
                if (Gate::allows('edit', [$company])) {
                    //se pode editar empresa pode visualizar os saques
                    $withdrawals = $withdrawalModel->where('company_id', $companyId)
                        ->orderBy('id', 'DESC');

                    return WithdrawalResource::collection($withdrawals->paginate(5));
                } else {
                    return [
                        'message' => 'Sem permissão para visualizar saques',
                    ];
                }
            } else {
                //id incorreto
                return [
                    'message' => 'Empresa não encontrada',
                ];
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar lista de saques (FinanceApiService - getTransactions)');
            report($e);

            return [
                'message' => 'Erro ao visualizar saques',
            ];
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalances(Request $request)
    {
        try {
            /** @var Company $companyModel */
            $companyModel = new Company();
            /** @var Transaction $transactionModel */
            $transactionModel   = new Transaction();
            $antecipableBalance = 0;
            $pendingBalance     = 0;
            if ($request->has('company') && !empty($request->input('company'))) {
                $companyId = current(Hashids::decode($request->input('company')));
                /** @var Company $company */
                $company = $companyModel->newQuery()->find($companyId);
                if (!empty($company)) {
                    //                    $pendingTransactions = $transactionModel->newQuery()->where('company_id', $company->id)
                    //                                                            ->where('status', 'paid')
                    //                                                            ->whereDate('release_date', '>', now()->startOfDay())
                    //                                                            ->get();
                    $pendingTransactions = $transactionModel->newQuery()->where('company_id', $company->id)
                        ->where('status', 'paid')
                        ->whereDate('release_date', '>', now()->startOfDay())
                        ->select(DB::raw('sum( value ) as pending_balance'))
                        ->first();
                    $pendingBalance      += $pendingTransactions->pending_balance;
                    $anticipableTransactions = $transactionModel->newQuery()->where('company_id', $company->id)
                        ->where('status', 'anticipated')
                        ->whereDate('release_date', '>', now()->startOfDay())
                        ->select(DB::raw('sum( value - antecipable_value ) as pending_balance'))
                        ->first();
                    $pendingBalance += $anticipableTransactions->pending_balance;
                    $antecipableTransactions = $transactionModel->newQuery()->where('company_id', $company->id)
                        ->where('status', 'paid')
                        ->whereDate('release_date', '>', Carbon::today())
                        ->whereDate('antecipation_date', '<=', Carbon::today())
                        ->select(DB::raw('sum( antecipable_value ) as antecipable_balance'))
                        ->first();

                    $antecipableBalance += $antecipableTransactions->antecipable_balance;
                    $availableBalance = $company->balance;
                    $totalBalance     = $availableBalance + $pendingBalance;

                    return [
                            'available_balance'   => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                            'antecipable_balance' => number_format(intval($antecipableBalance) / 100, 2, ',', '.'),
                            'total_balance'       => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                            'pending_balance'     => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                            'currency'            => $company->country == 'usa' ? '$' : 'R$',
                        ];
                } else {
                    return ['message' => 'Ocorreu algum erro, tente novamente!'];
                }
            } else {
                return ['message' => 'Ocorreu algum erro, tente novamente!'];
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar da empresa (FinanceApiController - getBalances)');
            report($e);

            return [
                'message' => 'Ocorreu algum erro, tente novamente!',
            ];
        }
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function getAccountInformation(Request $request) {
        /** @var Company $companyModel */
        $companyModel = new Company();
        /** @var BankService $bankService */
        $bankService = new BankService();
        /** @var User $userModel */
        $userModel = new User();

        if ($request->has('company') && !empty($request->input('company'))) {
            /** @var Company $company */
            $company = $companyModel->find(current(Hashids::decode($request->input('company'))));
        } else {
            return ['message' => 'Não foi possível recuperar o companyId! (FinanceApiService - getAccountInformation)'];
        }
        if (Gate::allows('edit', [$company])) {
            /** @var User $user */
            $user = $userModel->where('id', auth()->user()->account_owner_id)->first();
            if ($user->address_document_status != $userModel->present()->getAddressDocumentStatus('approved') ||
                $user->personal_document_status != $userModel->present()->getPersonalDocumentStatus('approved')) {
                return [
                    'message' => 'success',
                    'data'    => [
                        'user_documents_status' => 'pending',
                    ]
                ];
            }

            if (!$user->email_verified) {
                return [
                    'message' => 'success',
                    'data'    => [
                        'email_verified' => 'false',
                    ]
                ];
            }

            if (!$user->cellphone_verified) {
                return [
                    'message' => 'success',
                    'data'    => [
                        'cellphone_verified' => 'false',
                    ]
                ];
            }

            if ($company->bank_document_status == $companyModel->present()->getBankDocumentStatus('approved') &&
                $company->address_document_status == $companyModel->present()->getAddressDocumentStatus('approved') &&
                $company->contract_document_status == $companyModel->present()->getContractDocumentStatus('approved')) {

                // Verificar se telefone e e-mail estão verificados

                return [
                    'message' => 'success',
                    'data'    => [
                        'documents_status' => 'approved',
                        'bank'             => $bankService->getBankName($company->bank),
                        'account'          => $company->account,
                        'account_digit'    => $company->account_digit,
                        'agency'           => $company->agency,
                        'agency_digit'     => $company->agency_digit,
                        'document'         => $company->company_document,
                    ]
                ];
            } else {
                return [
                    'message' => 'success',
                    'data'    => [
                        'documents_status' => 'pending',
                    ]
                ];
            }
        } else {
            return ['message' => 'Sem permissão para visualizar dados da conta'];
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function store(Request $request)
    {
        $data = $request->all();
        /** @var Withdrawal $withdrawalModel */
        $withdrawalModel = new Withdrawal();
        /** @var Company $companyModel */
        $companyModel = new Company();
        /** @var Company $company */
        $company = $companyModel->where('user_id', auth()->user()->account_owner_id)
            ->find(current(Hashids::decode($data['company_id'])));
        if (Gate::allows('edit', [$company])) {
            if (!$company->bank_document_status == $companyModel->present()->getBankDocumentStatus('approved') ||
                !$company->address_document_status == $companyModel->present()->getAddressDocumentStatus('approved') ||
                !$company->contract_document_status == $companyModel->present()
                    ->getContractDocumentStatus('approved')) {
                return response()->json([
                    'message' => 'error',
                    'data'    => [
                        'documents_status' => 'pending',
                    ],
                ], 400);
            }
            $withdrawalValue = preg_replace("/[^0-9]/", "", $data['withdrawal_value']);
            $companyDocument = preg_replace("/[^0-9]/", "", $company->company_document);

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

            /** Se o cliente não tiver cadastrado um CNPJ, libera saque somente de 1900 por mês. */
            if (strlen($companyDocument) == 11) {
                $startDate  = Carbon::now()->startOfMonth();
                $endDate    = Carbon::now()->endOfMonth();
                $withdrawal = $withdrawalModel->where('company_id', $company->id)
                    ->where('status', $withdrawalModel->present()->getStatus('transfered'))
                    ->whereBetween('created_at', [$startDate, $endDate])->get();
                if (count($withdrawal) > 0) {
                    $withdrawalSum = $withdrawal->sum('value');
                    if ($withdrawalSum + $withdrawalValue > 190000) {
                        return response()->json([
                            'message' => 'Valor de saque máximo no mês para pessoa física é até R$ 1.900,00',
                        ], 400);
                    }
                }
            }

            $company->update(['balance' => $company->balance -= $withdrawalValue]);

            /** Saque abaixo de R$500,00 a taxa cobrada é R$10,00, acima disso a taxa é gratuita */
            if ($withdrawalValue < 50000) {
                $withdrawalValue -= 1000;
            }

            $withdrawal = $withdrawalModel->create(
                [
                    'value'         => $withdrawalValue,
                    'company_id'    => $company->id,
                    'bank'          => $company->bank,
                    'agency'        => $company->agency,
                    'agency_digit'  => $company->agency_digit,
                    'account'       => $company->account,
                    'account_digit' => $company->account_digit,
                    'status'        => $companyModel->present()->getStatus('pending'),
                ]
            );
            event(new WithdrawalRequestEvent($withdrawal));

            return response()->json(['message' => 'Saque pendente'], 200);
        } else {
            return response()->json(['message' => 'Sem permissão para salvar saques'], 403);
        }
    }
}
