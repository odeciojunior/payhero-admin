<?php

namespace Modules\Withdrawals\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Entities\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\BankService;
use Spatie\Activitylog\Models\Activity;
use Modules\Core\Services\CompanyService;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Core\Services\RemessaOnlineService;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Withdrawals\Transformers\WithdrawalResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
            $withdrawalModel = new Withdrawal();
            $companyModel    = new Company();
            $companyId       = current(Hashids::decode($request->company));

            if (empty($request->input('page')) || $request->input('page') == '1') {
                activity()->on($withdrawalModel)->tap(function(Activity $activity) {
                    $activity->log_name = 'visualization';
                })->log('Visualizou tela todas as transferências');
            }

            if ($companyId) {

                $company = $companyModel->find($companyId);

                if (Gate::allows('edit', [$company])) {
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
                if ($company->company_type == 1) {
                    $startDate = Carbon::now()->startOfMonth();

                    $endDate = Carbon::now()->endOfMonth();

                    $withdrawal    = $withdrawalModel->where('company_id', $company->id)
                                                     ->whereNotIn('status', collect([
                                                                                        $withdrawalModel->present()
                                                                                                        ->getStatus('returned'),
                                                                                        $withdrawalModel->present()
                                                                                                        ->getStatus('refused'),
                                                                                    ]))
                                                     ->whereBetween('created_at', [$startDate, $endDate])->get();
                    $withdrawalSum = 0;
                    if (count($withdrawal) > 0) {
                        $withdrawalSum = $withdrawal->sum('value');
                    }

                    if ($withdrawalSum + $withdrawalValue > 190000) {
                        return response()->json([
                                                    'message' => 'Valor de saque máximo no mês para pessoa física é até R$ 1.900,00',
                                                ], 400);
                    }
                }

                $company->update(['balance' => $company->balance -= $withdrawalValue]);

                /** Verifica se o usuário possui algum saque pendente */
                $withdrawal = $withdrawalModel->where([
                                                          ['company_id', $company->id],
                                                          ['status', $companyModel->present()->getStatus('pending')],
                                                      ])
                                              ->first();

                if (empty($withdrawal)) {
                    $tax = 0;

                    /**
                     *  Taxa cobrada de R$10,00 quando o saque abaixo de R$500,00.
                     */
                    if ($withdrawalValue < 50000) {
                        $withdrawalValue -= 1000;
                        $tax             = 1000;
                    }
                    $isFirstUserWithdrawal = false;
                    $userWithdrawal        = $withdrawalModel->whereHas('company', function($query) {
                        $query->where('user_id', auth()->user()->account_owner_id);
                    })->first();
                    if (empty($userWithdrawal)) {
                        $isFirstUserWithdrawal = true;
                    }
                    $withdrawal = $withdrawalModel->create(
                        [
                            'value'         => $withdrawalValue,
                            'company_id'    => $company->id,
                            'bank'          => $company->bank,
                            'agency'        => $company->agency,
                            'agency_digit'  => $company->agency_digit,
                            'account'       => $company->account,
                            'account_digit' => $company->account_digit,
                            'status'        => $withdrawalModel->present()
                                                               ->getStatus($isFirstUserWithdrawal ? 'in_review' : 'pending'),
                            'tax'           => $tax,
                            'observation'   => $isFirstUserWithdrawal ? 'Primeiro saque' : null,
                        ]
                    );
                } else {

                    $withdrawalValueSum = $withdrawal->value + $withdrawalValue;
                    $withdrawalTax      = $withdrawal->tax;

                    /**
                     *  Se a soma dos saques pendentes for maior de R$500,00
                     *  e havia sido cobrada taxa de R$10.00, os R$10.00 são devolvidos.
                     */
                    if (!empty($withdrawal->tax) && $withdrawalValueSum > 50000) {
                        $withdrawalValueSum += 1000;
                        $withdrawalTax      = 0;
                    }

                    $withdrawal->update([
                                            'value' => $withdrawalValueSum,
                                            'tax'   => $withdrawalTax,
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
    public function getAccountInformation(Request $request)
    {
        $data = $request->all();

        $companyModel = new Company();

        $bankService    = new BankService();
        $companyService = new CompanyService();

        $userModel = new User();

        $company = $companyModel->find(current(Hashids::decode($data['company_id'])));
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

                $withdrawalValue = preg_replace("/[^0-9]/", "", $data['withdrawal_value']);

                $convertedMoney = $withdrawalValue;

                $iofValue            = 0;
                $iofTax              = 0.38;
                $costValue           = 0;
                $costTax             = auth()->user()->abroad_transfer_tax;
                $abroadTransferValue = 0;
                $abroadTax           = $costTax + $iofTax;

                $companyService = new CompanyService();

                $currency         = $companyService->getCurrency($company);
                $currentQuotation = 0;

                if (!in_array($company->country, ['brazil', 'brasil'])) {

                    $remessaOnlineService = new RemessaOnlineService();

                    $currentQuotation = $remessaOnlineService->getCurrentQuotation($currency);

                    $iofValue            = intval($withdrawalValue / 100 * $iofTax);
                    $costValue           = intval($withdrawalValue / 100 * $costTax);
                    $abroadTransferValue = $iofValue + $costValue;
                    $withdrawalValue     -= $abroadTransferValue;
                    $convertedMoney      = number_format(intval($withdrawalValue / $currentQuotation) / 100, 2, ',', '.');
                }

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
                            'currency'         => $currency,
                            'quotation'        => $currentQuotation,
                            'abroad_transfer'  => [
                                'tax'             => $abroadTax,
                                'value'           => number_format(intval($abroadTransferValue) / 100, 2, ',', '.'),
                                'converted_money' => $convertedMoney,
                            ],
                            'iof'              => [
                                'tax'   => $iofTax,
                                'value' => number_format(intval($iofValue) / 100, 2, ',', '.'),
                            ],
                            'cost'             => [
                                'tax'   => $costTax,
                                'value' => number_format(intval($costValue) / 100, 2, ',', '.'),
                            ],
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


