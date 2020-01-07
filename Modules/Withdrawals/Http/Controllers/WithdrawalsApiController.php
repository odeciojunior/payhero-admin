<?php

namespace Modules\Withdrawals\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\BankService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Withdrawals\Transformers\WithdrawalResource;
use Laracasts\Presenter\Exceptions\PresenterException;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class WithdrawalsApiController
 * @package Modules\Withdrawals\Http\Controllers
 */
class WithdrawalsApiController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            /** @var Withdrawal $withdrawalModel */
            $withdrawalModel = new Withdrawal();
            /** @var Company $companyModel */
            $companyModel = new Company();
            $companyId    = current(Hashids::decode($request->company));

            if (empty($request->input('page')) || $request->input('page') == '1') {
                activity()->on($withdrawalModel)->tap(function(Activity $activity) {
                    $activity->log_name = 'visualization';
                })->log('Visualizou tela todas as transferências');
            }

            if ($companyId) {
                //id existe
                $company = $companyModel->find($companyId);
                if (Gate::allows('edit', [$company])) {
                    //se pode editar empresa pode visualizar os saques
                    $withdrawals = $withdrawalModel->where('company_id', $companyId)
                                                   ->orderBy('id', 'DESC');

                    return WithdrawalResource::collection($withdrawals->paginate(5));
                } else {
                    return response()->json([
                                                'message' => 'Sem permissão para visualizar saques',
                                            ], 403);
                }
            } else {
                //id incorreto
                return response()->json([
                                            'message' => 'Empresa não encontrada',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar lista de saques (WithdrawalsController - index)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao visualizar saques',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws PresenterException
     */
    public function store(Request $request)
    {

        $userModel = new User();

        if (auth()->user()->status != $userModel->present()->getStatus('withdrawal blocked')) {
            $data = $request->all();

            $withdrawalModel = new Withdrawal();

            $companyModel = new Company();

            $company = $companyModel->find(current(Hashids::decode($data['company_id'])));

            if (Gate::allows('edit', [$company])) {
                if (!$company->bank_document_status == $companyModel->present()->getBankDocumentStatus('approved') ||
                    !$company->address_document_status == $companyModel->present()
                                                                       ->getAddressDocumentStatus('approved') ||
                    !$company->contract_document_status == $companyModel->present()
                                                                        ->getContractDocumentStatus('approved')) {
                    return response()->json([
                                                'message' => 'error',
                                                'data'    => [
                                                    'documents_status' => 'pending',
                                                ],
                                            ], 400);
                }
                $withdrawalValue = preg_replace("/[^0-9]/", "", $data['withdrawal_value']);
                $companyDocument = preg_replace("/[^0-9]/", "", $company->company_document);

                if ($withdrawalValue < 1000) {
                    return response()->json([
                                                'message' => 'Valor de saque precisa ser maior que R$ 10,00',
                                            ], 400);
                }

                if ($withdrawalValue > $company->balance) {
                    return response()->json([
                                                'message' => 'Valor informado inválido',
                                            ], 400);
                }

                /** Se o cliente não tiver cadastrado um CNPJ, libera saque somente de 1900 por mês. */
                if (strlen($companyDocument) == 11) {
                    $startDate = Carbon::now()->startOfMonth();

                    $endDate = Carbon::now()->endOfMonth();

                    $withdrawal = $withdrawalModel->where('company_id', $company->id)
                                                  ->where('status', $withdrawalModel->present()
                                                                                    ->getStatus('transfered'))
                                                  ->whereBetween('created_at', [$startDate, $endDate])->get();

                    if (count($withdrawal) > 0) {

                        $withdrawalSum = $withdrawal->sum('value');

                        if ($withdrawalSum + $withdrawalValue > 190000) {
                            return response()->json([
                                                        'message' => 'Valor de saque máximo no mês para pessoa física é até R$ 1.900,00',
                                                    ], 400);
                        }
                    }
                }

                $company->update(['balance' => $company->balance -= $withdrawalValue]);

                /** Saque abaixo de R$500,00 a taxa cobrada é R$10,00, acima disso a taxa é gratuita */
                if ($withdrawalValue < 50000) {
                    $withdrawalValue -= 1000;
                }

                /** Verifica se o usuário já fez alguma solicitação de saque hoje */
                $withdrawal = $withdrawalModel->where([
                                                          ['company_id', $company->id],
                                                          ['status',     $companyModel->present()->getStatus('pending')],
                                                      ])
                                              ->first();

                if (empty($withdrawal)) {
                    $withdrawal = $withdrawalModel->create(
                        [
                            'value'         => $withdrawalValue,
                            'company_id'    => $company->id,
                            'bank'          => $company->bank,
                            'agency'        => $company->agency,
                            'agency_digit'  => $company->agency_digit,
                            'account'       => $company->account,
                            'account_digit' => $company->account_digit,
                            'status'        => $companyModel->present()->getStatus('pending'),
                        ]
                    );
                } else {

                    $withdrawal->update([
                                            'value' => $withdrawal->value + $withdrawalValue,
                                        ]);
                }

                event(new WithdrawalRequestEvent($withdrawal));

                return response()->json(['message' => 'Saque pendente'], 200);
            } else {
                return response()->json(['message' => 'Sem permissão para salvar saques'], 403);
            }
        } else {
            return response()->json(['message' => 'Solicitação de saque bloqueada pela administração. Contate o suporte.'], 400);
        }
    }

    /**
     * @param $companyId
     * @return JsonResponse
     * @throws PresenterException
     */
    public function getAccountInformation($companyId)
    {
        $companyModel = new Company();

        $bankService    = new BankService();
        $companyService = new CompanyService();

        $userModel = new User();

        $company = $companyModel->find(current(Hashids::decode($companyId)));
        if (Gate::allows('edit', [$company])) {

            $user = $userModel->where('id', auth()->user()->account_owner_id)->first();
            if ($user->address_document_status != $userModel->present()->getAddressDocumentStatus('approved') ||
                $user->personal_document_status != $userModel->present()->getPersonalDocumentStatus('approved')) {
                return response()->json(
                    [
                        'message' => 'success',
                        'data'    => [
                            'user_documents_status' => 'pending',
                        ],
                    ], 200
                );
            }

            if (!$user->email_verified) {
                return response()->json(
                    [
                        'message' => 'success',
                        'data'    => [
                            'email_verified' => 'false',
                        ],
                    ], 200
                );
            }

            if (!$user->cellphone_verified) {
                return response()->json(
                    [
                        'message' => 'success',
                        'data'    => [
                            'cellphone_verified' => 'false',
                        ],
                    ], 200
                );
            }

            if ($companyService->isDocumentValidated($company->id)) {

                // Verificar se telefone e e-mail estão verificados
                return response()->json(
                    [
                        'message' => 'success',
                        'data'    => [
                            'documents_status' => 'approved',
                            'bank'             => $bankService->getBankName($company->bank),
                            'account'          => $company->account,
                            'account_digit'    => $company->account_digit,
                            'agency'           => $company->agency,
                            'agency_digit'     => $company->agency_digit,
                            'document'         => $company->company_document,
                        ],
                    ], 200
                );
            } else {
                return response()->json(
                    [
                        'message' => 'success',
                        'data'    => [
                            'documents_status' => 'pending',
                        ],
                    ], 200
                );
            }
        } else {
            return response()->json(['message' => 'Sem permissão para visualizar dados da conta'], 403);
        }
    }

    /**
     * @return JsonResponse
     * @throws PresenterException
     */
    public function checkAllowed()
    {
        try {
            $userModel = new User();

            return response()->json([
                                        'allowed' => auth()->user()->status != $userModel->present()
                                                                                         ->getStatus('withdrawal blocked'),
                                    ]);
        } catch (Exception $e) {
            Log::warning('Erro ao verificar permisssão de saqea (WithdrawalsApiController - checkAllowed)');
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
        }
    }
}


