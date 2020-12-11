<?php

namespace Modules\Transfers\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Transfers\Services\GetNetStatementService;
use Modules\Transfers\Transformers\TransfersResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class TransfersController
 * @package Modules\Transfers\Http\Controllers
 */
class TransfersController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $transfersModel = new Transfer();

            activity()->on($transfersModel)->tap(function (Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos os extratos');

            $data = $request->all();

            //parâmetros obrigatórios
            $companyId = current(Hashids::decode($data['company']));
            $dateRange = FoxUtils::validateDateRange($data["date_range"]);
            if ($data['date_type'] == 'transaction_date') {
                $dateType = 'transaction.created_at';
            } else {
                if ($data['date_type'] == 'transfer_date') {
                    $dateType = 'transfers.created_at';
                } else {
                    $dateType = 'sales.start_date';
                }
            }

            $transfers = $transfersModel->leftJoin('transactions as transaction', 'transaction.id',
                'transfers.transaction_id')
                ->leftJoin('sales', 'sales.id', '=', 'transaction.sale_id')
                ->where(function ($query) use ($companyId) {
                    $query->where('transfers.company_id', $companyId)
                        ->orWhere('transaction.company_id', $companyId);
                })
                ->whereBetween($dateType, [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59'])
                ->whereNull('transfers.customer_id');

            $saleId = str_replace('#', '', $data['transaction']);
            $saleId = current(Hashids::connection('sale_id')->decode($saleId));

            if ($saleId) {
                $transfers = $transfers->where('transaction.sale_id', $saleId)
                    ->orWhere('transfers.anticipation_id', $saleId);
            }

            if (!empty($data['type'])) {
                $transfers->where('transfers.type_enum', $transfersModel->present()->getTypeEnum($data['type']));
            }

            if (!empty($data['reason'])) {
                $transfers->where('transfers.reason', 'like', '%' . $data['reason'] . '%');
            }

            if (!empty($data['value'])) {
                $value = intval(preg_replace('/[^0-9]/', '', $data['value']));
                $transfers->where('transfers.value', $value);
            }

            $balanceInPeriod = $transfers->selectRaw("sum(CASE WHEN transfers.type_enum = 2 THEN (transfers.value * -1) ELSE transfers.value END) as balanceInPeriod")
                ->first();

            if (!empty($balanceInPeriod)) {
                $balanceInPeriod = $balanceInPeriod->balanceInPeriod / 100;
                $balanceInPeriod = number_format($balanceInPeriod, 2, ',', '.');
            }
            $transfers = $transfers->whereNull('transfers.customer_id');
            $transfers = $transfers->select(
                'transfers.*',
                'transaction.sale_id',
                'transaction.company_id',
                'transaction.currency',
                'transaction.status',
                'transaction.type as transaction_type',
            )->orderBy('id', 'DESC')
                ->paginate(10);
            $return = TransfersResource::collection($transfers);

            $return->additional([
                'meta' => [
                    'balance_in_period' => $balanceInPeriod,
                ],
            ]);

            return $return;
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Ocorreu um erro, tente novamente mais tarde!',
            ], 400);
        }
    }

    public function accountStatementData()
    {
        try {
            $companyGetNet = Company::whereNotNull('subseller_getnet_id')
                ->where('user_id', auth()->user()->account_owner_id)
                ->whereGetNetStatus(1)
                ->whereId(current(Hashids::decode(request()->get('company'))))
                ->first();

            if (empty($companyGetNet)) {
                return response()->json([]);
            }

            $subseller = $companyGetNet->subseller_getnet_homolog_id;
            if (FoxUtils::isProduction()) {
                $subseller = $companyGetNet->subseller_getnet_id;
            }

            try {

                $dates = explode(' - ', request('dateRange') ?? '');

                if (is_array($dates) && count($dates) == 2) {

                    // Quando enviarmos o daterange
                    $startDate = Carbon::createFromFormat('d/m/Y', $dates[0]);
                    $endDate = Carbon::createFromFormat('d/m/Y', $dates[1]);

                } else if (is_array($dates) && count($dates) == 1) {

                    // Quando enviarmos uma data única com o input type="date"
                    $startDate = Carbon::createFromFormat('Y-m-d', $dates[0]);
                    $endDate = $startDate;
                }
            } catch (Exception $exception) {
            }

            if (!isset($startDate) || !isset($endDate)) {

                $today = today();
                $startDate = $today;
                $endDate = $today;
            }

            if (request('statement_data_type') == 'schedule_date') {

                $statementDateField = GetnetBackOfficeService::STATEMENT_DATE_SCHEDULE;

            } elseif (request('statement_data_type') == 'liquidation_date') {

                $statementDateField = GetnetBackOfficeService::STATEMENT_DATE_LIQUIDATION;

            } else {

                $statementDateField = GetnetBackOfficeService::STATEMENT_DATE_TRANSACTION;
            }

            $getNetBackOfficeService = new GetnetBackOfficeService();
            $getNetBackOfficeService->setStatementSubSellerId($subseller)
                ->setStatementStartDate($startDate)
                ->setStatementEndDate($endDate)
                ->setStatementDateField($statementDateField);

            if (!empty(request('sale'))) {

                $getNetBackOfficeService->setStatementSaleHashId(request('sale'));
            }

            $result = $getNetBackOfficeService->getStatement();
            $result = json_decode($result);

            if (isset($result->errors)) {
                return response()->json($result->errors, 400);
            }

            $transactions = (new GetNetStatementService())->performStatement($result);
            $transactions = collect($transactions);

            return response()->json($transactions);
        } catch (Exception $exception) {
            report($exception);

            $error = [
                'message' => 'Ocorreu um erro, tente novamente mais tarde!',
            ];

            if (!FoxUtils::isProduction()) {
                $error += [
                    'dev_message' => $exception->getMessage(),
                    'dev_file' => $exception->getFile(),
                    'dev_line' => $exception->getLine(),
                    'dev_code' => $exception->getCode(),
                    'dev_trace' => $exception->getTrace(),
                ];
            }

            return response()->json($error, 400);
        }
    }
}


