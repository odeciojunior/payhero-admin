<?php

namespace Modules\Api\Http\Controllers\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Api\Http\Requests\V1\WithdrawalsApiRequest;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\UserService;
use Modules\Withdrawals\Services\WithdrawalService;
use Spatie\Activitylog\Models\Activity;

class WithdrawalsApiController extends Controller
{
    public function storeWithdrawals(WithdrawalsApiRequest $request)
    {
        try {
            $settings = settings()
                ->group("withdrawal_request")
                ->get("withdrawal_request", null, true);

            if ($settings != null && $settings == false) {
                return response()->json(["message" => "Tente novamente em alguns minutos"], 400);
            }

            $user = User::find($request->user_id);

            if ((new UserService())->userWithdrawalBlocked($user)) {
                return response()->json(["message" => "Sem permissão para realizar saque"], 403);
            }

            $company = Company::find($request->company_id);

            if (!Gate::allows("edit", [$company])) {
                return response()->json(["message" => "Sem permissão para realizar saque"], 403);
            }

            if (!(new WithdrawalService())->companyCanWithdraw($company->id, $request->gateway_id)) {
                return response()->json(["message" => "Você só pode fazer 3 pedidos de saque por dia"], 403);
            }

            $gatewayService = Gateway::getServiceById($request->gateway_id)->setCompany($company);

            activity()
                ->on(new Withdrawal())
                ->tap(function (Activity $activity) {
                    $activity->log_name = "created";
                })
                ->log("Solicitou Saque");

            $withdrawalValue = (int) foxutils()->onlyNumbers($request->withdrawal_value);

            if (!$gatewayService->withdrawalValueIsValid($withdrawalValue)) {
                return response()->json(["message" => "Valor para saque inválido"], 400);
            }

            if (!$gatewayService->existsBankAccountApproved()) {
                return response()->json(["message" => "Cadastre um meio de recebimento para solicitar saques"], 400);
            }

            $response = $gatewayService->createWithdrawal($withdrawalValue);

            if ($response) {
                return response()->json(["message" => "Saque em processamento"], 200);
            }

            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde!"], 400);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde!"], 403);
        }
    }
}
