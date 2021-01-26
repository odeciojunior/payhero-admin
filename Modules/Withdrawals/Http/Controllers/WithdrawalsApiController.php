<?php

namespace Modules\Withdrawals\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Withdrawals\Transformers\WithdrawalResource;
use Modules\Withdrawals\Transformers\WithdrawalTransactionsResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Withdrawals\Exports\Reports\WithdrawalsReportExport;
use PDOException;
use DB;

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

            try {
                DB::beginTransaction();
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
            } catch (PDOException $e) {
                DB::rollBack();
                report($e);
                return response()->json(['message' => 'Ocorreu um erro, tente novamnte mais tarde!'], 403);
            }

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
                    'lower_value' => 0,
                    'bigger_value' => 0
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

    public function getTransactionsByBrand($id)
    {

        try {

            $withdrawalId = current(Hashids::decode($id));
            $withdrawalModel = new Withdrawal();

            if (empty($withdrawalId)) {
                return response()->json(['message' => 'Erro ao exibir detalhes do saque'], 400);
            }

            $withdrawal = $withdrawalModel->with('company')->find($withdrawalId);
            if (FoxUtils::isProduction()) {
                $subsellerGetnetId = $withdrawal->company->subseller_getnet_id;
            } else {
                $subsellerGetnetId = $withdrawal->company->subseller_getnet_homolog_id;
            }

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

                if ((!$transaction->sale->flag || empty($transaction->sale->flag)) && $transaction->sale->payment_method == 1) {
                    $transaction->sale->flag = 'generico';
               }
                elseif ($transaction->sale->payment_method == 2) {
                    $transaction->sale->flag = 'boleto';
               }

                if ( !$transaction->gateway_transferred  and ($withdrawal->status == 3 or $withdrawal->status == 9 or $withdrawal->status == 8 )) {

                    $getNetBackOfficeService = new GetnetBackOfficeService();

                    $getNetBackOfficeService->setStatementSubSellerId($subsellerGetnetId)
                        ->setStatementSaleHashId($transaction->sale->hash_id);

                    $originalResult = $getNetBackOfficeService->getStatement();

                    $gatewaySale = json_decode($originalResult);
                    if (!empty($gatewaySale->list_transactions[0]) &&
                        !empty($gatewaySale->list_transactions[0]->details[0]) &&
                        !empty($gatewaySale->list_transactions[0]->details[0]->subseller_rate_confirm_date)
                    ) {
                        $date = str_replace('T', ' ', $gatewaySale->list_transactions[0]->details[0]->subseller_rate_confirm_date);
                        $date = date("d/m/Y", strtotime($date));

                        $transaction->update(
                            [
                                'gateway_transferred' => true,
                            ]
                        );

                        $this->updateArrayBrands($arrayBrands, $transaction, true, $date );

                    } else {

                        $this->updateArrayBrands($arrayBrands, $transaction, false );
                    }
                }
                else {
                    $this->updateArrayBrands($arrayBrands, $transaction, true );
                }

            }

            $arrayTransactions = [];

            foreach ($arrayBrands as $arrayBrand) {

                if ($arrayBrand['liquidated'] == true and  empty($arrayBrand['date'])) {

                    $subSeller = $subsellerGetnetId;

                    $getNetBackOfficeService = new GetnetBackOfficeService();

                    $getNetBackOfficeService->setStatementSubSellerId($subSeller)
                        ->setStatementSaleHashId($arrayBrand['hash_id']);


                    $originalResult = $getNetBackOfficeService->getStatement();

                    $gatewaySale = json_decode($originalResult);
                    if (!empty($gatewaySale->list_transactions[0]) &&
                        !empty($gatewaySale->list_transactions[0]->details[0]) &&
                        !empty($gatewaySale->list_transactions[0]->details[0]->subseller_rate_confirm_date)
                    ) {
                        $date = str_replace('T', ' ', $gatewaySale->list_transactions[0]->details[0]->subseller_rate_confirm_date);
                        $date = date("d/m/Y", strtotime($date));

                        $arrayBrand['date'] = $date;
                    }
                }
                //dd($arrayBrand);
                $arrayTransactions[] = [
                    'brand' => $arrayBrand['brand'],
                    'value' => 'R$' . number_format(intval($arrayBrand['value']) / 100, 2, ',', '.'),
                    'liquidated' => $arrayBrand['liquidated'],
                    'date' =>  $arrayBrand['date'] ?? ' - ',
                ];
            }


            $return = [
                'id' => $id,
                'date_request' => $withdrawal->created_at->format('d/m/Y'),
                'total_withdrawal' => 'R$' . number_format(intval($total_withdrawal) / 100, 2, ',', '.'),
                'transactions' =>  $arrayTransactions,
            ];

            return response()->json($return, 200);

        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde!'], 400);
        }


    }

    public function updateArrayBrands (Array &$arrayBrands, $transaction, $isLiquidated, $date = null) {

        if (array_key_exists($transaction->sale->flag,$arrayBrands))
        {
            if (!$isLiquidated) {
                $arrayBrands[$transaction->sale->flag]['liquidated'] = false;
            }

            $arrayBrands[$transaction->sale->flag]['value'] += $transaction->value;
        }
        else
        {
            $arrayBrands[$transaction->sale->flag] = [
                'brand' => $transaction->sale->flag,
                'value' => $transaction->value,
                'liquidated' => $isLiquidated,
                'date' =>  $date,
                'hash_id' => $transaction->sale->hash_id,

            ];
        }

    }

    public function getTransactions(Request $request, $id)
    {
        try {
            $dataRequest = \request()->all();
            $withdrawalId = current(Hashids::decode($id));

            activity()->tap(function (Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Exportou tabela ' . $dataRequest['format'] . ' da agenda financeira');
//
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
