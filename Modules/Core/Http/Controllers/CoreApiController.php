<?php

namespace Modules\Core\Http\Controllers;

use Carbon\Carbon;
use DateTime;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\BonusBalance;
use Modules\Core\Entities\Ticket;
use Modules\Core\Events\Sac\NotifyTicketClosedEvent;
use Modules\Core\Events\Sac\NotifyTicketMediationEvent;
use Modules\Core\Events\Sac\NotifyTicketOpenEvent;
use Modules\Core\Events\UserRegistrationFinishedEvent;
use Modules\Core\Transformers\CompaniesSelectResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Entities\UserInformation;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\Gateways\Safe2PayService;
use Modules\Core\Services\UserService;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

class CoreApiController extends Controller
{
    public function verifyAccount($id)
    {
        try {
            $userService = new UserService();

            $companyModel = new Company();
            $companyService = new CompanyService();

            $user = User::find(hashids_decode($id));

            $userInformations = UserInformation::where('document', $user->document)->exists();

            $userStatus = null;
            $userAddressDocument = $user->present()->getAddressDocumentStatus($user->address_document_status);
            $userPersonaltDocument = $user->present()->getPersonalDocumentStatus($user->personal_document_status);
            $userRedirect = null;
            if ($userService->haveAnyDocumentPending()) {
                $userStatus = $user->present()->getAddressDocumentStatus(UserDocument::STATUS_PENDING);
                $userRedirect = '/personal-info';
            }

            if ($userService->haveAnyDocumentAnalyzing()) {
                $userStatus = $user->present()->getAddressDocumentStatus(UserDocument::STATUS_ANALYZING);
                $userRedirect = '/personal-info';
            }

            if ($userService->haveAnyDocumentApproved()) {
                $userStatus = $user->present()->getAddressDocumentStatus(UserDocument::STATUS_APPROVED);
                $userRedirect = '/personal-info';
            }

            if ($userService->haveAnyDocumentRefused()) {
                $userStatus = $user->present()->getAddressDocumentStatus(UserDocument::STATUS_REFUSED);
                $userRedirect = '/personal-info';
            }

            $companyStatus = null;
            $companyAddressDocument = null;
            $companyContractDocument = null;
            $companyRedirect = null;

            $companies = Company::where('user_id', auth()->user()->account_owner_id)->where('active_flag', true)->get();

            if ($companies->count() == 0) {
                $companyStatus = null;
                $companyRedirect = '/companies';
            } else {
                $companyApproved = $companyService->companyDocumentApproved();
                if (!empty($companyApproved)) {
                    $companyStatus = $companyModel->present()->getStatus(CompanyDocument::STATUS_APPROVED);
                    $companyAddressDocument = $companyModel->present()->getAddressDocumentStatus($companyApproved->address_document_status);
                    $companyContractDocument = $companyModel->present()->getContractDocumentStatus($companyApproved->contract_document_status);
                    $companyRedirect = '/companies';
                } else {
                    $companyPending = $companyService->companyDocumentPending();
                    if (!empty($companyPending)) {
                        $companyStatus = $companyModel->present()->getStatus(CompanyDocument::STATUS_PENDING);
                        $companyAddressDocument = $companyModel->present()->getAddressDocumentStatus($companyPending->address_document_status);
                        $companyContractDocument = $companyModel->present()->getContractDocumentStatus($companyPending->contract_document_status);
                        $companyRedirect = '/companies/company-detail/'. hashids_decode($companyPending->id);
                    }

                    $companyAnalyzing = $companyService->companyDocumentAnalyzing();
                    if (!empty($companyAnalyzing)) {
                        $companyStatus = $companyModel->present()->getStatus(CompanyDocument::STATUS_ANALYZING);
                        $companyAddressDocument = $companyModel->present()->getAddressDocumentStatus($companyAnalyzing->address_document_status);
                        $companyContractDocument = $companyModel->present()->getContractDocumentStatus($companyAnalyzing->contract_document_status);
                        $companyRedirect = '/companies/company-detail/'. hashids_decode($companyAnalyzing->id);
                    }

                    $companyRefused = $companyService->companyDocumentRefused();
                    if (!empty($companyRefused)) {
                        $companyStatus = $companyModel->present()->getStatus(CompanyDocument::STATUS_REFUSED);
                        $companyAddressDocument = $companyModel->present()->getAddressDocumentStatus($companyRefused->address_document_status);
                        $companyContractDocument = $companyModel->present()->getContractDocumentStatus($companyRefused->contract_document_status);
                        $companyRedirect = '/companies/company-detail/'. hashids_decode($companyRefused->id);
                    }
                }
            }

            $this->updateUserStatus($user, $userInformations, $userStatus, $companyStatus);

            return response()->json([
                'data' => [
                    'account' => [
                        'status' => $user->present()->getAccountStatus($user->account_is_approved),
                        'type' => $user->present()->getAccountType($user->id, $user->account_owner_id),
                    ],
                    'user' => [
                        'status' => $userStatus,
                        'address_document' => $userAddressDocument,
                        'personal_document' => $userPersonaltDocument,
                        'document' => $user->document,
                        'email' => $user->email,
                        'informations' => $userInformations,
                        'link' => $userRedirect,
                    ],
                    'company' => [
                        'status' => $companyStatus,
                        'address_document' => $companyAddressDocument,
                        'contract_document' => $companyContractDocument,
                        'link' => $companyRedirect,
                    ]
                ]
            ], Response::HTTP_OK);
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateUserStatus($user, $userInformations, $userStatus, $companyStatus)
    {
        if (!$user->account_is_approved) {
            if ($userStatus == 'approved' && $companyStatus == 'approved' && $userInformations) {
                $user->update([
                    'account_is_approved' => 1
                ]);
            }
        }
    }

    public function verifyDocuments()
    {
        try {
            $companyService = new CompanyService();
            $userService = new UserService();
            $userModel = new User();

            $userDocumentRefused = $userService->haveAnyDocumentRefused();

            $link = null;
            $refused = false;
            $analyzing = false;
            $user = auth()->user();
            $accountType = ($user->id == $user->account_owner_id) ? 'owner' : 'collaborator';

            if ($userDocumentRefused) {
                $refused = true;
                $link = '/personal-info';
            } else {
                $companyDocumentRefused = $companyService->companyDocumentRefused();
                $companyDocumentApproved = $companyService->companyDocumentApproved();
                if (empty($companyDocumentApproved) && !empty($companyDocumentRefused)) {
                    $refused = true;
                    $companyCode = hashids_decode($companyDocumentRefused->id);
                    if ($companyDocumentRefused->company_type == $companyDocumentRefused->present()->getCompanyType(
                            'physical person'
                        )
                    ) {
                        $link = "/personal-info";
                    } else {
                        $link = "/companies/company-detail/${companyCode}";
                    }
                } else {
                    $userValid = $userService->isDocumentValidated();
                    if (!$userValid) {
                        $analyzing = true;
                    } else {
                        if (!auth()->user()->account_is_approved) {
                            $analyzing = true;
                        }
                    }
                }
            }

            if (env('ACCOUNT_FRONT_URL') && empty($link)) {
                $link = env('ACCOUNT_FRONT_URL') . $link;
            }

            return response()->json(
                [
                    'message' => 'Documentos verificados!',
                    'analyzing' => $analyzing,
                    'refused' => $refused,
                    'link' => $link,
                    'accountType' => $accountType,
                    'accountStatus' => $userModel->present()->getStatus($user->status),
                ]
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(['error' => 'Erro ao verificar documentos'], 400);
        }
    }

    public function companies(Request $request)
    {
        try {
            $companyService = new CompanyService();

            $paginate = true;
            if ($request->has('select') && $request->input('select')) {
                $paginate = false;
            }

            return $companyService->getCompaniesUser($paginate);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde',
                ],
                400
            );
        }
    }

    public function getCompanies()
    {
        try {
            $companyModel = new Company();
            $companies = $companyModel->newQuery()->where('user_id', auth()->user()->account_owner_id)
                ->orderBy('order_priority')->get();

            return CompaniesSelectResource::collection($companies);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro ao tentar buscar dados, tente novamente mais tarde',
                ],
                400
            );
        }
    }

    public function allowBlockBalance($companyId, $saleId)
    {
        try {
            $company = Company::find(hashids_decode($companyId));
            $sale = Sale::find(hashids_decode($saleId, 'sale_id'));

            if (empty($company) || empty($sale)) {
                return response()->json('Dados inválidos', 400);
            }

            $safe2payService = new Safe2PayService();
            $safe2payService->setCompany($company);

            $availableBalance = $safe2payService->getAvailableBalance();
            $pendingBalance = $safe2payService->getPendingBalance();

            (new CompanyService)->applyBlockedBalance($safe2payService, $availableBalance, $pendingBalance);

            $transaction = Transaction::where('sale_id', $sale->id)->where('company_id', $company->id)->first();

            $response = (object)[
                'allow_block' => ($availableBalance + $pendingBalance) >= $transaction->value
            ];

            return response()->json($response);
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao verificar bloqueio da comissão!'], 400);
        }
    }

    public function notifyTicket($ticketId)
    {
        try {
            $id = hashids_decode($ticketId);
            $ticket = Ticket::find($id);

            switch ($ticket->ticket_status_enum) {
                case Ticket::STATUS_OPEN:
                    event(new NotifyTicketOpenEvent($ticket->id));
                    break;
                case Ticket::STATUS_CLOSED:
                    event(new NotifyTicketClosedEvent($ticket->id));
                    break;
                case Ticket::STATUS_MEDIATION:
                    event(new NotifyTicketMediationEvent($ticket->id));
                    break;
            }

            return response()->json(['message' => 'Sucesso']);
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao notificar alteração no chamado!'], 400);
        }
    }

    public function getBonusBalance()
    {
        $bonusBalance = BonusBalance::where('user_id', auth()->user()->account_owner_id)
                                    ->where('expires_at', '>=', today())
                                    ->where('current_value', '>', 0)
                                    ->first();

        if(empty($bonusBalance)) {
            return response()->json([
                'error' => 'bonus balance not found'
            ]);
        }

        return response()->json([
            'user_name' => auth()->user()->present()->firstName(),
            'total_bonus' => foxutils()->formatMoney($bonusBalance->total_value / 100),
            'current_bonus' => foxutils()->formatMoney($bonusBalance->current_value / 100),
            'used_bonus' => foxutils()->formatMoney(($bonusBalance->total_value - $bonusBalance->current_value) / 100),
            'expires_at' => Carbon::parse($bonusBalance->expires_at)->format('d/m/Y'),
            'used_percentage' => floor(100 - ($bonusBalance->current_value * 100 / $bonusBalance->total_value))
        ]);
    }

    public function checkDocumentOnBureau(string $userId) {
        try {
            $user = User::findOrFail(hashids_decode($userId));
            event(new UserRegistrationFinishedEvent($user));
            return response('', Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            report($e);
            return response('User not found.', 404);
        } catch (Exception $e) {
            report($e);
            return response('Internal server error.', 500);
        }
    }
}
