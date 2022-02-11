<?php

namespace Modules\Withdrawals\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Core\Entities\WithdrawalSettings;
use Modules\Core\Services\FoxUtils;
use Modules\Withdrawals\Http\Requests\WithdrawalSettingsRequest;
use Modules\Withdrawals\Transformers\WithdrawalSettingsResource;
use Vinkla\Hashids\Facades\Hashids;

class WithdrawalsSettingsApiController
{
    public function index(Request $request)
    {
        try {
            $withdrawalSettingsModel = new WithdrawalSettings();
            $companyModel = new Company();
            $companyId = current(Hashids::decode($request->company));

//            if (empty($request->input('page')) || $request->input('page') == '1') {
//                activity()->on($withdrawalSettingsModel)->tap(
//                    function (Activity $activity) {
//                        $activity->log_name = 'visualization';
//                    }
//                )->log('Visualizou tela configurações do saque automático');
//            }

            if (empty($companyId)) {
                return response()->json(['message' => 'Empresa não encontrada',], 404);
            }

            $company = $companyModel->find($companyId);

            if (!Gate::allows('edit', [$company])) {
                return response()->json(['message' => 'Sem permissão para visualizar saques',], 403);
            }

            $withdrawals = $withdrawalSettingsModel->where('company_id', $companyId)->orderBy('id', 'DESC');

            return WithdrawalSettingsResource::collection($withdrawals->paginate(10));
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao visualizar saques',], 400);
        }
    }

    /**
     * Show the specified resource.
     * @param string $companyId
     * @param string $settingsId
     * @return JsonResponse|WithdrawalSettingsResource
     */
    public function show($companyId, $settingsId = null)
    {
        try {
            $companyId = current(Hashids::decode($companyId));
            if ($companyId) {
                $withdrawalSettingsModel = new WithdrawalSettings();
                $settingsId = current(Hashids::decode($settingsId));
                if ($settingsId) {
                    $settings = $withdrawalSettingsModel->findOrFail($settingsId);
                } else {
                    $settings = $withdrawalSettingsModel->where('company_id', $companyId)
                            ->whereNull('deleted_at')
                            ->orderBy('id', 'desc')
                            ->first() ?? null;
                }

                if ($settings) {
                    return new WithdrawalSettingsResource($settings);
                } else {
                    return response()->json(['message' => 'Nenhuma configuração de saques automáticos foi encontrada'],
                        404);
                }
            } else {
                return response()->json(['message' => 'Erro ao carregar configurações de saques automáticos da empresa'],
                    400);
            }
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(WithdrawalSettingsRequest $request): JsonResponse
    {
        try {
            $requestData = $request->validated();

            $company = Company::find(hashids_decode($requestData['company_id']));

            if (!Gate::allows('edit', [$company]) ||
                in_array(Auth::user()->status, [User::STATUS_ACCOUNT_BLOCKED, User::STATUS_WITHDRAWAL_BLOCKED])
            ) {
                return response()->json(['message' => 'Sem permissão para salvar configurações de saques'], 403);
            }

            $withdrawalSettings = WithdrawalSettings::firstOrNew(
                [
                    'company_id' => $company->id,
                ]
            );

            $withdrawalSettings->rule = $requestData['rule'];
            $withdrawalSettings->frequency = $requestData['frequency'] ?? null;
            $withdrawalSettings->weekday = $requestData['weekday'] ?? null;
            $withdrawalSettings->day = $requestData['day'] ?? null;
            $withdrawalSettings->amount = !empty($requestData['amount']) ? FoxUtils::onlyNumbers($requestData['amount']) : null;
            $withdrawalSettings->save();
            return response()->json([
                'message' => 'Configurações de saques automáticos salvos com sucesso',
                'data' => WithdrawalSettingsResource::make($withdrawalSettings)
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Ocorreu um erro, tente novamnte mais tarde! ' . $e->getMessage()],
                500);
        }
    }

    /**
     * @param WithdrawalSettingsRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(WithdrawalSettingsRequest $request, $id): JsonResponse
    {
        try {
            $requestData = $request->validated();

            $company = Company::find(hashids_decode($requestData['company_id']));

            $withdrawalSettings = WithdrawalSettings::find(current(Hashids::decode($id)));

            if (!Gate::allows('edit', [$company]) ||
                in_array(Auth::user()->status, [User::STATUS_ACCOUNT_BLOCKED, User::STATUS_WITHDRAWAL_BLOCKED])
            ) {
                return response()->json(['message' => 'Sem permissão para salvar configurações de saques'], 403);
            }

            $withdrawalSettings->update(
                [
                    'rule' => $requestData['rule'],
                    'frequency' => $requestData['frequency'] ?? null,
                    'weekday' => $requestData['weekday'] ?? null,
                    'day' => $requestData['day'] ?? null,
                    'amount' => !empty($requestData['amount']) ? FoxUtils::onlyNumbers($requestData['amount']) : null,
                ]
            );

            return response()->json([
                'message' => 'Configurações de saques automáticos salvos com sucesso',
                'data' => WithdrawalSettingsResource::make($withdrawalSettings)
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Ocorreu um erro, tente novamnte mais tarde! ' . $e->getMessage()],
                500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            if (empty($id)) {
                return response()->json(['message' => 'Configuração de saque automático não encontrado'], 404);
            }

            $withdrawalSettingsModel = new WithdrawalSettings();
            $settings = $withdrawalSettingsModel->find(current(Hashids::decode($id)));

            if (!empty($settings)) {
                $settingsDeleted = $settings->delete();
                if ($settingsDeleted) {
                    return response()->json(['message' => 'Configuração de saque automático deletada com sucesso!']);
                }
            }

            return response()->json(['message' => 'Erro ao deletar Configuração de saque automático'], 400);
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao deletar Configuração de saque automático'], 400);
        }
    }
}
