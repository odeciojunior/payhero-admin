<?php

namespace Modules\Withdrawals\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Core\Services\BankService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\RemessaOnlineService;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\UserService;
use Modules\Withdrawals\Transformers\WithdrawalResource;
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
     * @return JsonResponse|AnonymousResourceCollection
     */
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

            if ($companyId) {
                $company = $companyModel->find($companyId);

                if (Gate::allows('edit', [$company])) {
                    $withdrawals = $withdrawalModel->where('company_id', $companyId)
                        ->orderBy('id', 'DESC');

                    return WithdrawalResource::collection($withdrawals->paginate(5));
                } else {
                    return response()->json(
                        [
                            'message' => 'Sem permissão para visualizar saques',
                        ],
                        403
                    );
                }
            } else {
                //id incorreto
                return response()->json(
                    [
                        'message' => 'Empresa não encontrada',
                    ],
                    400
                );
            }
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
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

            $userPresent = (new User())->present();
            $user = auth()->user();

            if ($user->status == $userPresent->getStatus('withdrawal blocked')) {
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
            $userService = new UserService();

            if ($companyService->verifyFieldsEmpty($company) || $userService->verifyFieldsEmpty($user)) {
                return response()->json(
                    ['message' => 'Para efetuar o saque favor preencher os documentos pendentes'],
                    403
                );
            }

            if (!$company->bank_document_status == $companyModel->present()
                    ->getBankDocumentStatus('approved') ||
                !$company->address_document_status == $companyModel->present()
                    ->getAddressDocumentStatus('approved') ||
                !$company->contract_document_status == $companyModel->present()
                    ->getContractDocumentStatus('approved')) {
                return response()->json(
                    [
                        'message' => 'error',
                        'data' => [
                            'documents_status' => 'pending',
                        ],
                    ],
                    400
                );
            }
            $withdrawalValue = preg_replace("/[^0-9]/", "", $data['withdrawal_value']);

            if ($withdrawalValue < 1000) {
                return response()->json(
                    [
                        'message' => 'Valor de saque precisa ser maior que R$ 10,00',
                    ],
                    400
                );
            }

            if ($withdrawalValue > $company->balance) {
                return response()->json(
                    [
                        'message' => 'Valor informado inválido',
                    ],
                    400
                );
            }

            // verify blocked balance
            $blockedValue = $companyService->getBlockedBalance($company->id, auth()->user()->account_owner_id);

            $availableBalance = $company->balance - $blockedValue;

            if ($withdrawalValue > $availableBalance) {
                return response()->json(
                    [
                        'message' => 'Valor informado inválido',
                    ],
                    400
                );
            }

            /** Se o cliente não tiver cadastrado um CNPJ, libera saque somente de 1900 por mês. */
            if ($company->company_type == $companyModel->present()->getCompanyType('physical person')) {
                $startDate = Carbon::now()->startOfMonth();

                $endDate = Carbon::now()->endOfMonth();

                $withdrawal = $withdrawalModel->where('company_id', $company->id)
                    ->whereNotIn(
                        'status',
                        collect(
                            [
                                $withdrawalModel->present()
                                    ->getStatus('returned'),
                                $withdrawalModel->present()
                                    ->getStatus('refused'),
                            ]
                        )
                    )
                    ->whereBetween('created_at', [$startDate, $endDate])->get();
                $withdrawalSum = 0;
                if (count($withdrawal) > 0) {
                    $withdrawalSum = $withdrawal->sum('value');
                }

                if ($withdrawalSum + $withdrawalValue > 190000) {
                    return response()->json(
                        [
                            'message' => 'Valor de saque máximo no mês para pessoa física é até R$ 1.900,00',
                        ],
                        400
                    );
                }
            }

            $company->update(['balance' => $company->balance -= $withdrawalValue]);

            /** Verifica se o usuário possui algum saque pendente */
            $withdrawal = $withdrawalModel->where(
                [
                    ['company_id', $company->id],
                    [
                        'status',
                        $companyModel->present()->getStatus('pending')
                    ],
                ]
            )->first();

            if (empty($withdrawal)) {
                $tax = 0;

                if ($withdrawalValue < 50000) {
                    $withdrawalValue -= 1000;
                    $tax = 1000;
                }

                /** Verifica se é o primeiro saque do usuário */
                $isFirstUserWithdrawal = false;
                $userWithdrawal = $withdrawalModel->whereHas(
                    'company',
                    function ($query) {
                        $query->where('user_id', auth()->user()->account_owner_id);
                    }
                )->where('status', $withdrawalModel->present()->getStatus('transfered'))->first();
                if (empty($userWithdrawal)) {
                    $isFirstUserWithdrawal = true;
                }

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
                        'tax' => $tax,
                        'observation' => $isFirstUserWithdrawal ? 'Primeiro saque' : null,
                    ]
                );
            } else {
                $withdrawalValueSum = $withdrawal->value + $withdrawalValue;
                $withdrawalTax = $withdrawal->tax;

                /**
                 *  Se a soma dos saques pendentes for maior de R$500,00
                 *  e havia sido cobrada taxa de R$10.00, os R$10.00 são devolvidos.
                 */
                if (!empty($withdrawal->tax) && $withdrawalValueSum > 50000) {
                    $withdrawalValueSum += 1000;
                    $withdrawalTax = 0;
                }

                $withdrawal->update(
                    [
                        'value' => $withdrawalValueSum,
                        'tax' => $withdrawalTax,
                    ]
                );
            }

            event(new WithdrawalRequestEvent($withdrawal));

            return response()->json(['message' => 'Saque pendente'], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro, tente novamnte mais tarde!'], 403);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAccountInformation(Request $request)
    {
        try {
            $companyModel = new Company();

            $data = $request->all();

            $company = $companyModel->find(current(Hashids::decode($data['company_id'])));
            if (!Gate::allows('edit', [$company])) {
                return response()->json(['message' => 'Sem permissão para visualizar dados da conta'], 403);
            }

            $bankService = new BankService();
            $userModel = new User();
            $companyService = new CompanyService();

            $user = $userModel->where('id', auth()->user()->account_owner_id)->first();

            if ($user->address_document_status != $userModel->present()->getAddressDocumentStatus('approved')
                || $user->personal_document_status != $userModel->present()->getPersonalDocumentStatus('approved')
            ) {
                return response()->json(
                    [
                        'message' => 'success',
                        'data' => [
                            'user_documents_status' => 'pending',
                        ],
                    ],
                    200
                );
            }

            if (!$user->email_verified) {
                return response()->json(
                    [
                        'message' => 'success',
                        'data' => [
                            'email_verified' => 'false',
                        ],
                    ],
                    200
                );
            }

            if (!$user->cellphone_verified) {
                return response()->json(
                    [
                        'message' => 'success',
                        'data' => [
                            'cellphone_verified' => 'false',
                        ],
                    ],
                    200
                );
            }

            if (!$companyService->isDocumentValidated($company->id)) {
                return response()->json(
                    [
                        'message' => 'success',
                        'data' => [
                            'documents_status' => 'pending',
                        ],
                    ],
                    200
                );
            }

            $withdrawalValue = preg_replace("/[^0-9]/", "", $data['withdrawal_value']);

            $convertedMoney = $withdrawalValue;

            $iofValue = 0;
            $iofTax = 0.38;
            $costValue = 0;

            $abroadTransferValue = 0;

            $currency = $companyService->getCurrency($company);
            $currentQuotation = 0;

            if (!in_array($company->country, ['brazil', 'brasil'])) {
                $remessaOnlineService = new RemessaOnlineService();

                $currentQuotation = $remessaOnlineService->getCurrentQuotation($currency);

                $iofValue = intval($withdrawalValue / 100 * $iofTax);

                $withdrawalValue -= $abroadTransferValue;
                $convertedMoney = number_format(
                    intval($withdrawalValue / $currentQuotation) / 100,
                    2,
                    ',',
                    '.'
                );
            }

            return response()->json(
                [
                    'message' => 'success',
                    'data' => [
                        'documents_status' => 'approved',
                        'bank' => $bankService->getBankName($company->bank),
                        'account' => $company->account,
                        'account_digit' => $company->account_digit,
                        'agency' => $company->agency,
                        'agency_digit' => $company->agency_digit,
                        'document' => $company->document,
                        'currency' => $currency,
                        'quotation' => $currentQuotation,
                        'iof' => [
                            'tax' => $iofTax,
                            'value' => number_format(intval($iofValue) / 100, 2, ',', '.'),
                        ],
                    ],
                ],
                200
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde!'], 403);
        }
    }

    /**
     * @return JsonResponse
     */
    public function checkAllowed()
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
}


