<?php

namespace Modules\Anticipations\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Transfer;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Controllers\Controller;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Anticipation;
use Modules\Core\Entities\AnticipatedTransaction;

class AnticipationsApiController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            if ($request->has('company') && !empty($request->has('company'))) {
                $companyModel                 = new Company();
                $transactionModel             = new Transaction();
                $anticipationModel            = new Anticipation();
                $anticipationTransactionModel = new AnticipatedTransaction();
                $transferModel                = new Transfer();
                $company                      = $companyModel->find(current(Hashids::decode($request->input('company'))));

                if (!empty($company)) {

                    $antecipableTransactions = $transactionModel->where('company_id', $company->id)
                                                                ->where('status', 'paid')
                                                                ->whereDate('release_date', '>', Carbon::today())
                                                                ->whereDate('antecipation_date', '<=', Carbon::today())
                                                                ->get();

                    $user     = auth()->user();
                    $dailyTax = number_format(($user->antecipation_tax / $user->release_money_days), 4, '.', ',');

                    $taxValue          = 0;
                    $pendingBalance    = 0;
                    $percentageTax     = 0;
                    $anticipationArray = [];

                    if (count($antecipableTransactions) > 0) {
                        foreach ($antecipableTransactions as $anticipableTransaction) {
                            $diffInDays = Carbon::now()->diffInDays($anticipableTransaction->release_date);
                            $diffInDays += 1;

                            $percentageTax = $diffInDays * $dailyTax;

                            $taxValue += number_format(intval(($anticipableTransaction->value * $percentageTax)) / 10000, 2, '.', ',');

                            $anticipationArray[] = [
                                'tax_value'     => preg_replace('/\D/', '', $taxValue),
                                'tax'           => preg_replace('/\D/', '', $percentageTax),
                                'daysToRelease' => preg_replace('/\D/', '', $diffInDays),
                                'transactionId' => preg_replace('/\D/', '', $anticipableTransaction->id),
                            ];

                            $pendingBalance += preg_replace('/\D/', '', $anticipableTransaction->antecipable_value);

                            $anticipableTransaction->update([
                                                                'status' => 'anticipated',
                                                            ]);
                        }

                        $anticipation = $anticipationModel->create([
                                                                       'value'              => $pendingBalance,
                                                                       'tax'                => preg_replace('/\D/', '', number_format($taxValue, 2, '.', ',')),
                                                                       'percentage_tax'     => $user->antecipation_tax,
                                                                       'release_money_days' => $user->release_money_days,
                                                                       'company_id'         => $company->id,
                                                                   ]);

                        if (!empty($anticipation)) {
                            foreach ($anticipationArray as $item) {
                                $anticipationTransactionModel->create([
                                                                          'tax'             => preg_replace('/\D/', '', $item['tax']),
                                                                          'tax_value'       => preg_replace('/\D/', '', $item['tax_value']),
                                                                          'days_to_release' => preg_replace('/\D/', '', $item['daysToRelease']),
                                                                          'anticipation_id' => preg_replace('/\D/', '', $anticipation->id),
                                                                          'transaction_id'  => preg_replace('/\D/', '', $item['transactionId']),

                                                                      ]);
                            }

                            $company->update([
                                                 'balance' => preg_replace('/\D/', '', number_format(intval(($company->balance + $pendingBalance) - preg_replace('/\D/', '', $taxValue)) / 100, 2, '.', ',')),
                                             ]);

                            $transferModel->create([
                                                       'user_id'       => $user->id,
                                                       'company_id' => $company->id,
                                                       'value'      => preg_replace('/\D/', '', intval($pendingBalance - preg_replace('/\D/', '', $taxValue))),
                                                       'type'       => 'in',
                                                       'type_enum'  => 1,
                                                       'reason'     => 'Antecipação',
                                                   ]);

                            return response()->json([
                                                        'message' => 'Saldo antecipado com successo!',

                                                    ], 200);
                        } else {
                            return response()->json([
                                                        'message' => 'Erro ao tentar antecipar valor',
                                                    ], 400);
                        }
                    } else {

                        return response()->json([
                                                    'message' => 'Você não tem saldo disponivel para antecipar!',
                                                    'data'    => [
                                                        'valueAntecipable' => '0,00',
                                                        'taxValue'         => '0,00',
                                                    ],
                                                ], 400);
                    }
                } else {

                    return response()->json([
                                                'message' => 'Erro ao buscar dados da empresa',
                                            ], 400);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @param $company
     * @return JsonResponse
     */
    public function show(Request $request, $company)
    {
        try {

            $companyModel     = new Company();
            $transactionModel = new Transaction();

            if (!empty($company)) {
                $companyId = current(Hashids::decode($company));

                $company = $companyModel->find($companyId);

                if (!empty($company)) {
                    $antecipableTransactions = $transactionModel->where('company_id', $company->id)
                                                                ->where('status', 'paid')
                                                                ->whereDate('release_date', '>', Carbon::today())
                                                                ->whereDate('antecipation_date', '<=', Carbon::today())
                                                                ->get();

                    $user     = auth()->user();
                    $dailyTax = number_format(($user->antecipation_tax / $user->release_money_days), 4, '.', ',');

                    $taxValue       = 0;
                    $pendingBalance = 0;
                    $percentageTax  = 0;
                    if (count($antecipableTransactions) > 0) {
                        foreach ($antecipableTransactions as $anticipableTransaction) {
                            $diffInDays = Carbon::now()->diffInDays($anticipableTransaction->release_date);

                            $percentageTax = $diffInDays * $dailyTax;

                            $taxValue += number_format(intval(($anticipableTransaction->value * $percentageTax)) / 10000, 2, '.', ',');

                            $pendingBalance += preg_replace('/\D/', '', $anticipableTransaction->antecipable_value);
                        }

                        return response()->json([
                                                    'message' => 'success',
                                                    'data'    => [
                                                        'valueAntecipable' => number_format(intval(($company->balance + $pendingBalance) - preg_replace('/\D/', '', $taxValue)) / 100, 2, '.', ','),
                                                        'taxValue'         => number_format($taxValue, 2, '.', ','),
                                                    ],
                                                ], 200);
                    } else {

                        return response()->json([
                                                    'message' => 'Você não tem saldo disponivel para antecipar!',
                                                    'data'    => [
                                                        'valueAntecipable' => 0, 00,
                                                        'taxValue'         => 0, 00,
                                                    ],
                                                ], 400);
                    }
                } else {
                    return response()->json([
                                                'message' => 'Erro ao buscar dados da empresa',
                                            ], 400);
                }
            } else {

                return response()->json([
                                            'message' => 'Erro ao buscar dados da empresa',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao calcular antecipação ');
            report($e);
        }
    }

}
