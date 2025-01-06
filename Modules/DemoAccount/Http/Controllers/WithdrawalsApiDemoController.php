<?php

namespace Modules\DemoAccount\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\UserService;
use Modules\Withdrawals\Http\Controllers\WithdrawalsApiController;
use Vinkla\Hashids\Facades\Hashids;

class WithdrawalsApiDemoController extends WithdrawalsApiController
{
    public function getWithdrawalValues(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            $company = Company::find(hashids_decode($data['company_id']));
            $gatewayId = hashids_decode($data['gateway_id']);

            $withdrawalValueRequested = (int) FoxUtils::onlyNumbers($data['withdrawal_value']);

            $gatewayService = Gateway::getServiceById($gatewayId)->setCompany($company);

            return response()->json($gatewayService->getLowerAndBiggerAvailableValues($withdrawalValueRequested));

        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => __('messages.unexpected_error')], 403);
        }
    }

    public function getAccountInformation(Request $request): JsonResponse
    {
        try {

            return response()->json([
                'message' => 'Sem documentos pendentes',
                'data' => []
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => __('messages.unexpected_error')], 403);
        }
    }
}
