<?php

namespace Modules\Withdrawals\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\CompanyBalanceService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Core\Services\UserService;
use Modules\Withdrawals\Exports\Reports\WithdrawalsReportExport;
use Modules\Withdrawals\Services\WithdrawalService;
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
            $withdrawalService = new WithdrawalService();

            if ((new UserService())->userWithdrawalBlocked(auth()->user())) {
                return response()->json(['message' => 'Sem permissão para realizar saques'], 403);
            }

            $data = $request->only(['company_id', 'withdrawal_value']);

            $company = (new Company())->find(current(Hashids::decode($data['company_id'])));

            if (!Gate::allows('edit', [$company])) {
                return response()->json(['message' => 'Sem permissão para salvar saques'], 403);
            }

            if ($withdrawalService->isNotFirstWithdrawalToday($company)) {
                return response()->json(['message' => 'Você só pode fazer um pedido de saque por dia.'], 403);
            }

            $withdrawalValue = (int)FoxUtils::onlyNumbers($data['withdrawal_value']);

            $companyService = new CompanyBalanceService($company);
            $availableBalance = $companyService->getBalance(CompanyBalanceService::AVAILABLE_BALANCE);

            $pendingDebtsSum = $companyService->getBalance(CompanyBalanceService::PENDING_DEBT_BALANCE);

            if (!$withdrawalService->valueWithdrawalIsValid($withdrawalValue, $availableBalance, $pendingDebtsSum)) {
                return response()->json(
                    [
                        'message' => 'Valor informado inválido',
                    ],
                    400
                );
            }

            $responseCreateWithdrawal = $withdrawalService->createWithdrawal($withdrawalValue, $company);

            if ($responseCreateWithdrawal) {
                return response()->json(['message' => 'Saque processando.'], 200);
            }

            return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde!'], 403);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde!'], 403);
        }
    }

    public function getWithdrawalValues(Request $request): JsonResponse
    {
        try {

            $data = $request->all();

            $company = Company::find(current(Hashids::decode($data['company_id'])));
            if (!Gate::allows('edit', [$company])) {
                return response()->json(['message' => 'Sem permissão para visualizar dados da conta'], 403);
            }

            $withdrawalValueRequested = (int)FoxUtils::onlyNumbers($data['withdrawal_value']);

            return response()
                ->json((new WithdrawalService())->getLowerAndBiggerAvailableValues($company,$withdrawalValueRequested));

        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde!'], 403);
        }
    }

    public function checkAllowed(): JsonResponse
    {
        try {
            $userModel = new User();

            return response()->json([
                'allowed' => auth()->user()->status != $userModel->present()
                        ->getStatus('withdrawal blocked'),
            ]);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
        }
    }

    public function getTransactionsByBrand($id)
    {
        try {
            $withdrawalId = current(Hashids::decode($id));
            $withdrawalModel = new Withdrawal();

            if (empty($withdrawalId)) {
                return response()->json(['message' => 'Erro ao exibir detalhes do saque'], 400);
            }

            $withdrawal = $withdrawalModel->with('company')->find($withdrawalId);

            if (!Gate::allows('edit', [$withdrawal->company])) {
                return response()->json(
                    [
                        'message' => 'Sem permissão para visualizar saques',
                    ],
                    403
                );
            }

            $transactions = Transaction::with('sale')->with('company')->where('withdrawal_id', $withdrawalId)->get();

            $arrayBrands = [];
            $total_withdrawal = 0;
            foreach ($transactions as $transaction) {
                $total_withdrawal += $transaction->value;

                if (!$transaction->sale->flag || empty($transaction->sale->flag)) {
//                    $transaction->sale->flag = $transaction->sale->present()->getFlagPaymentMethod();
                    $transaction->sale->flag = $transaction->sale->present()->getPaymentFlag();
                }

                if (!empty($transaction->gateway_transferred_at)) {
                    $date = \Carbon\Carbon::parse($transaction->gateway_transferred_at)->format('d/m/Y');
                    $this->updateArrayBrands($arrayBrands, $transaction, true, $date);
                }
                else {
                    $this->updateArrayBrands($arrayBrands, $transaction, false);
                }
            }

            $arrayTransactions = [];

            foreach ($arrayBrands as $arrayBrand) {

                $arrayTransactions[] = [
                    'brand' => $arrayBrand['brand'],
                    'value' => 'R$' . number_format(intval($arrayBrand['value']) / 100, 2, ',', '.'),
                    'liquidated' => $arrayBrand['liquidated'],
                    'date' => $arrayBrand['date'] ?? ' - ',
                ];
            }

            $return = [
                'id' => $id,
                'date_request' => $withdrawal->created_at->format('d/m/Y'),
                'total_withdrawal' => 'R$' . number_format(intval($total_withdrawal) / 100, 2, ',', '.'),
                'debt_pending_value' => 'R$ ' . number_format(intval($withdrawal->debt_pending_value) / 100, 2, ',', '.'),
                'transactions' => $arrayTransactions,
            ];

            return response()->json($return, 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde!'], 400);
        }
    }

    public function updateArrayBrands(array &$arrayBrands, $transaction, $isLiquidated, $date = null)
    {
        if (array_key_exists($transaction->sale->flag, $arrayBrands)) {
            if (!$isLiquidated) {
                $arrayBrands[$transaction->sale->flag]['liquidated'] = false;
                $arrayBrands[$transaction->sale->flag]['date'] = null;
            }
            $arrayBrands[$transaction->sale->flag]['value'] += $transaction->value;
        } else {
            $arrayBrands[$transaction->sale->flag] = [
                'brand' => $transaction->sale->flag,
                'value' => $transaction->value,
                'liquidated' => $isLiquidated,
                'date' => $date,
                'gateway_order_id' => $transaction->sale->gateway_order_id,
            ];
        }
    }

    public function getTransactions(Request $request, $id)
    {
        try {
            $dataRequest = \request()->all();
            $withdrawalId = current(Hashids::decode($id));

            activity()->tap(
                function (Activity $activity) {
                    $activity->log_name = 'visualization';
                }
            )->log('Exportou tabela ' . $dataRequest['format'] . ' da agenda financeira');

            $user = auth()->user();
            $filename = 'withdrawals_report_' . Hashids::encode($user->id) . '.xls';
            $email = !empty($dataRequest['email']) ? $dataRequest['email'] : $user->email;

            //Excel::store(new WithdrawalsReportExport($withdrawalId, $user, $email, $filename), $filename);

            (new WithdrawalsReportExport($withdrawalId, $user, $email, $filename))
                ->queue($filename)->allOnQueue('high');

            return response()->json(['message' => 'A exportação começou', 'email' => $dataRequest['email']]);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
