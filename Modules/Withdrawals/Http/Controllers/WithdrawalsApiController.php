<?php

namespace Modules\Withdrawals\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Withdrawals\Transformers\WithdrawalResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class WithdrawalsApiController
{
    public function index(Request $request)
    {
        try {
            $withdrawalModel = new Withdrawal();
            $companyModel = new Company();
            $companyId = current(Hashids::decode($request->company));

            if (empty($request->input('page')) || $request->input('page') == '1') {
                activity()->on($withdrawalModel)->tap(
                    function (Activity $activity) {
                        $activity->log_name = 'visualization';
                    }
                )->log('Visualizou tela todas as transferências');
            }

            if (empty($companyId)) {
                return response()->json(
                    [
                        'message' => 'Empresa não encontrada',
                    ],
                    400
                );
            }

            $company = $companyModel->find($companyId);

            if (!Gate::allows('edit', [$company])) {
                return response()->json(
                    [
                        'message' => 'Sem permissão para visualizar saques',
                    ],
                    403
                );
            }

            $withdrawals = $withdrawalModel->where('company_id', $companyId)
                ->where('automatic_liquidation', 1)
                ->orderBy('id', 'DESC');

            return WithdrawalResource::collection($withdrawals->paginate(10));
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Erro ao visualizar saques',
                ],
                400
            );
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $settingsWithdrawalRequest = settings()->group('withdrawal_request')->get('withdrawal_request', null, true);

            if ($settingsWithdrawalRequest != null && $settingsWithdrawalRequest == false) {
                return response()->json(
                    [
                        'message' => 'Tente novamente em alguns minutos',
                    ],
                    400
                );
            }

            $user = auth()->user();

            if ($user->status == (new User())->present()->getStatus('withdrawal blocked')) {
                return response()->json(['message' => 'Sem permissão para realizar saques'], 403);
            }

            $withdrawalModel = new Withdrawal();
            $companyModel = new Company();

            $data = $request->all();

            $company = $companyModel->find(current(Hashids::decode($data['company_id'])));

            if (!Gate::allows('edit', [$company])) {
                return response()->json(['message' => 'Sem permissão para salvar saques'], 403);
            }

            $companyService = new CompanyService();

            $withdrawalValue = (int)FoxUtils::onlyNumbers($data['withdrawal_value']);
            $availableBalance = $companyService->getAvailableBalance(
                $company,
                CompanyService::STATEMENT_AUTOMATIC_LIQUIDATION_TYPE
            );

            if (empty($withdrawalValue) || $withdrawalValue < 1 || $withdrawalValue > $availableBalance) {
                return response()->json(
                    [
                        'message' => 'Valor informado inválido',
                    ],
                    400
                );
            }

            $withdrawalStatus = [
                $withdrawalModel->present()->getStatus('liquidating'),
                $withdrawalModel->present()->getStatus('partially_liquidated'),
                $withdrawalModel->present()->getStatus('transfered')
            ];

            $isFirstUserWithdrawal = false;
            $userWithdrawal = $withdrawalModel->whereHas(
                'company',
                function ($query) {
                    $query->where('user_id', auth()->user()->account_owner_id);
                }
            )
                ->whereIn('status', $withdrawalStatus)
                ->exists();

            if (!$userWithdrawal) {
                $isFirstUserWithdrawal = true;
            }

            $withdrawal = $withdrawalModel->create(
                [
                    'value' => $withdrawalValue,
                    'company_id' => $company->id,
                    'bank' => $company->bank,
                    'agency' => $company->agency,
                    'agency_digit' => $company->agency_digit,
                    'account' => $company->account,
                    'account_digit' => $company->account_digit,
                    'status' => $withdrawalModel->present()->getStatus(
                        $isFirstUserWithdrawal ? 'in_review' : 'pending'
                    ),
                    'tax' => 0,
                    'observation' => $isFirstUserWithdrawal ? 'Primeiro saque' : null,
                    'automatic_liquidation' => true,
                ]
            );

            $transactionsSum = $company->transactions()
                ->whereIn('gateway_id', [14, 15])
                ->where('is_waiting_withdrawal', 1)
                ->whereNull('withdrawal_id')
                ->orderBy('id');

            $currentValue = 0;

            $transactionsSum->chunkById(
                2000,
                $test = function ($transactions) use (
                    $currentValue,
                    $withdrawalValue,
                    $withdrawal
                ) {
                    foreach ($transactions as $transaction) {
                        $currentValue += $transaction->value;

                        if ($currentValue <= $withdrawalValue) {
                            $transaction->update(
                                [
                                    'withdrawal_id' => $withdrawal->id,
                                    'is_waiting_withdrawal' => false
                                ]
                            );
                        }
                    }
                }
            );

            $withdrawal->update(['value' => Transaction::where('withdrawal_id', $withdrawal->id)->sum('value')]);

            event(new WithdrawalRequestEvent($withdrawal));

            return response()->json(['message' => 'Saque pendente'], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro, tente novamnte mais tarde!'], 403);
        }
    }

    public function getWithdrawalValues(Request $request): JsonResponse
    {
        try {
            $companyModel = new Company();

            $data = $request->all();

            $company = $companyModel->find(current(Hashids::decode($data['company_id'])));
            if (!Gate::allows('edit', [$company])) {
                return response()->json(['message' => 'Sem permissão para visualizar dados da conta'], 403);
            }

            $withdrawalValueRequested = FoxUtils::onlyNumbers($data['withdrawal_value']);
            $currentValue = 0;

            $transactionsSum = $company->transactions()
                ->whereIn('gateway_id', [14, 15])
                ->where('is_waiting_withdrawal', 1)
                ->whereNull('withdrawal_id')
                ->orderBy('id');

            $transactionsSum->chunk(
                2000,
                function ($transactions) use (
                    $currentValue,
                    $withdrawalValueRequested
                ) {
                    foreach ($transactions as $transaction) {
                        $currentValue += $transaction->value;

                        if ($currentValue >= $withdrawalValueRequested) {
                            return response()->json(
                                [
                                    'data' => [
                                        'lower_value' => $currentValue - $transaction->value,
                                        'bigger_value' => $currentValue
                                    ]
                                ]
                            )->send();
                            exit();
                        }
                    }
                }
            );
            return response()->json(
                [
                    'data' => [
                        'lower_value' => 0,
                        'bigger_value' => 0
                    ]
                ]
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde!'], 403);
        }
    }

    public function checkAllowed(): JsonResponse
    {
        try {
            $userModel = new User();

            return response()->json(
                [
                    'allowed' => auth()->user()->status != $userModel->present()
                            ->getStatus('withdrawal blocked'),
                ]
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
        }
    }
}
