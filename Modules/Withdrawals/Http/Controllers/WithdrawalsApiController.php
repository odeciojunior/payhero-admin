<?php

namespace Modules\Withdrawals\Http\Controllers;

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
use Modules\Withdrawals\Transformers\WithdrawalResource;
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
            if ($companyId) {
                //id existe
                $company = $companyModel->newQuery()->find($companyId);
                if (Gate::allows('edit', [$company])) {
                    //se pode editar empresa pode visualizar os saques
                    $withdrawals = $withdrawalModel->newQuery()->where('company_id', $companyId)
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
        $data = $request->all();
        /** @var Withdrawal $withdrawalModel */
        $withdrawalModel = new Withdrawal();
        /** @var Company $companyModel */
        $companyModel = new Company();
        /** @var Company $company */
        $company = $companyModel->newQuery()->where('user_id', auth()->user()->id)
                                ->find(current(Hashids::decode($data['company_id'])));
        if (Gate::allows('edit', [$company])) {
            if (!$company->bank_document_status == $companyModel->present()->getBankDocumentStatus('approved') ||
                !$company->address_document_status == $companyModel->present()->getAddressDocumentStatus('approved') ||
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
            $company->update(['balance' => $company->balance -= $withdrawalValue]);
            $withdrawalValue -= 380;
            $withdrawal      = $withdrawalModel->newQuery()->create(
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
            event(new WithdrawalRequestEvent($withdrawal));

            return response()->json(['message' => 'Saque pendente'], 200);
        } else {
            return response()->json(['message' => 'Sem permissão para salvar saques'], 403);
        }
    }

    /**
     * @param $companyId
     * @return JsonResponse
     * @throws PresenterException
     */
    public function getAccountInformation($companyId)
    {
        /** @var Company $companyModel */
        $companyModel = new Company();
        /** @var BankService $bankService */
        $bankService = new BankService();
        /** @var User $userModel */
        $userModel = new User();
        /** @var Company $company */
        $company = $companyModel->newQuery()->find(current(Hashids::decode($companyId)));
        if (Gate::allows('edit', [$company])) {
            /** @var User $user */
            $user = $userModel->newQuery()->where('id', auth()->user()->id)->first();
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

            if ($company->bank_document_status == $companyModel->present()->getBankDocumentStatus('approved') &&
                $company->address_document_status == $companyModel->present()->getAddressDocumentStatus('approved') &&
                $company->contract_document_status == $companyModel->present()->getContractDocumentStatus('approved')) {

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
}


