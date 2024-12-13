<?php

namespace Modules\Transfers\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Finances\Exports\Reports\FinanceReportExport;
use Modules\Transfers\Services\GetNetStatementService;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class TransfersApiController
{
    public function index(Request $request)
    {
        try {
            $company = Company::find(hashids_decode($request->company_id));
            $gatewayId = hashids_decode($request->gateway_id);

            return Gateway::getServiceById($gatewayId)
                ->setCompany($company)
                ->getStatement($request->all());
        } catch (Exception $e) {
            report($e);
            return response()->json(
                [
                    "message" => __('messages.unexpected_error'),
                ],
                400
            );
        }
    }

    public function accountStatementData(): JsonResponse
    {
        try {
            $dataRequest = request()->all();

            if (!empty(request("sale"))) {
                request()->merge(["sale" => str_replace("#", "", request("sale"))]);
            }

            if (!empty($dataRequest["sale"])) {
                $dataRequest["sale"] = str_replace("#", "", $dataRequest["sale"]);
            }

            $filtersAndStatement = (new GetNetStatementService())->getFiltersAndStatement(
                hashids_decode($dataRequest["company"])
            );
            $filters = $filtersAndStatement["filters"];
            $result = json_decode($filtersAndStatement["statement"]);

            if (isset($result->errors)) {
                return response()->json($result->errors, 400);
            }
            $data = (new GetNetStatementService())->performWebStatement($result, $filters, 1000);
            return response()->json($data);
        } catch (Exception $exception) {
            report($exception);

            $error = [
                "message" => __('messages.unexpected_error'),
            ];

            $error += [
                "dev_message" => $exception->getMessage(),
                "dev_file" => $exception->getFile(),
                "dev_line" => $exception->getLine(),
                "dev_code" => $exception->getCode(),
                "dev_trace" => $exception->getTrace(),
            ];
            return response()->json($error, 400);
        }
    }

    public function accountStatementDataExport(): JsonResponse
    {
        try {
            $dataRequest = \request()->all();

            activity()
                ->tap(function (Activity $activity) {
                    $activity->log_name = "visualization";
                })
                ->log("Exportou tabela " . $dataRequest["format"] . " da agenda financeira");

            $user = auth()->user();
            $filename = "finances_report_" . Hashids::encode($user->id) . ".xls";

            (new FinanceReportExport($dataRequest, $user, $filename))->queue($filename)->allOnQueue("high");

            return response()->json(["message" => "A exportação começou", "email" => $dataRequest["email"]]);
        } catch (Exception $exception) {
            report($exception);

            $error = [
                "message" => __('messages.unexpected_error'),
            ];

            $error += [
                "dev_message" => $exception->getMessage(),
                "dev_file" => $exception->getFile(),
                "dev_line" => $exception->getLine(),
                "dev_code" => $exception->getCode(),
                "dev_trace" => $exception->getTrace(),
            ];
            return response()->json($error, 400);
        }
    }
}
