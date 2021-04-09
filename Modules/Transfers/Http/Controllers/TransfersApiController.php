<?php

namespace Modules\Transfers\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\FoxUtils;
use Modules\Finances\Exports\Reports\FinanceReportExport;
use Modules\Transfers\Services\GetNetStatementService;
use Modules\Transfers\Transformers\TransfersResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class TransfersApiController
{
    public function index(Request $request)
    {
        try {
            $transfersModel = new Transfer();

            activity()->on($transfersModel)->tap(
                function (Activity $activity) {
                    $activity->log_name = 'visualization';
                }
            )->log('Visualizou tela todos os extratos');

            $data = $request->all();

            $companyId = current(Hashids::decode($data['company']));
            $dateRange = FoxUtils::validateDateRange($data["date_range"]);
            if ($data['date_type'] == 'transaction_date') {
                $dateType = 'transaction.created_at';
            } elseif ($data['date_type'] == 'transfer_date') {
                $dateType = 'transfers.created_at';
            } else {
                $dateType = 'sales.start_date';
            }

            $transfers = $transfersModel->leftJoin(
                'transactions as transaction',
                'transaction.id',
                'transfers.transaction_id'
            )->leftJoin('sales', 'sales.id', '=', 'transaction.sale_id')
                ->where(
                    function ($query) use ($companyId) {
                        $query->where('transfers.company_id', $companyId)
                            ->orWhere('transaction.company_id', $companyId);
                    }
                )->whereBetween($dateType, [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59'])
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

            $balanceInPeriod = $transfers->selectRaw(
                "sum(CASE WHEN transfers.type_enum = 2 THEN (transfers.value * -1) ELSE transfers.value END) as balanceInPeriod"
            )->first();

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
                'transaction.antecipable_value'
            )->orderBy('id', 'DESC')->paginate(10);
            $return = TransfersResource::collection($transfers);

            $return->additional(
                [
                    'meta' => [
                        'balance_in_period' => $balanceInPeriod,
                    ],
                ]
            );

            return $return;
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                ],
                400
            );
        }
    }

    public function accountStatementData(): JsonResponse
    {
        try {

            $dataRequest = request()->all();
            $filtersAndStatement = (new GetNetStatementService())->getFiltersAndStatement($dataRequest);
            $filters = $filtersAndStatement['filters'];
            $result = json_decode($filtersAndStatement['statement']);

            if (isset($result->errors)) {
                return response()->json($result->errors, 400);
            }

            $data = (new GetNetStatementService())->performWebStatement($result, $filters, 1000);
            return response()->json($data);

        } catch (Exception $exception) {
            report($exception);

            $error = [
                'message' => 'Ocorreu um erro, tente novamente mais tarde!',
            ];

            $error += [
                'dev_message' => $exception->getMessage(),
                'dev_file' => $exception->getFile(),
                'dev_line' => $exception->getLine(),
                'dev_code' => $exception->getCode(),
                'dev_trace' => $exception->getTrace(),
            ];
            return response()->json($error, 400);
        }
    }

    public function accountStatementDataExport(): JsonResponse
    {

        try {

            $dataRequest = \request()->all();

            activity()->tap(function (Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Exportou tabela ' . $dataRequest['format'] . ' da agenda financeira');

            $user = auth()->user();
            $filename = 'finances_report_' . Hashids::encode($user->id) . '.xls';

            (new FinanceReportExport($dataRequest, $user, $filename))
              ->queue($filename)->allOnQueue('high');

            return response()->json(['message' => 'A exportação começou', 'email' => $dataRequest['email']]);


        } catch (Exception $exception) {
            report($exception);

            $error = [
                'message' => 'Ocorreu um erro, tente novamente mais tarde!',
            ];

            $error += [
                'dev_message' => $exception->getMessage(),
                'dev_file' => $exception->getFile(),
                'dev_line' => $exception->getLine(),
                'dev_code' => $exception->getCode(),
                'dev_trace' => $exception->getTrace(),
            ];
            return response()->json($error, 400);
        }
    }
}
