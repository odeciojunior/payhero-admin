<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Core\Entities\BonusBalance;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Entities\UserInformation;
use Modules\Core\Enums\User\UserBiometryStatusEnum;
use Modules\Core\Events\Sac\NotifyTicketClosedEvent;
use Modules\Core\Events\Sac\NotifyTicketMediationEvent;
use Modules\Core\Events\Sac\NotifyTicketOpenEvent;
use Modules\Core\Events\UserRegistrationFinishedEvent;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\Gateways\Safe2PayService;
use Modules\Core\Services\UserService;
use Modules\Core\Transformers\CompaniesSelectResource;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

final class CoreApiController extends Controller
{
    public function verifyAccount($id)
    {
        try {
            $userService = new UserService();

            $companyModel = new Company();
            $companyService = new CompanyService();

            $user = User::find(hashids_decode($id));

            $userInformations = UserInformation::where("document", $user->document)->exists();

            $userStatus = null;

            if ($userService->haveAnyDocumentPending()) {
                $userStatus = $user->present()->getAddressDocumentStatus(UserDocument::STATUS_PENDING);
            }

            if ($userService->haveAnyDocumentAnalyzing()) {
                $userStatus = $user->present()->getAddressDocumentStatus(UserDocument::STATUS_ANALYZING);
            }

            if ($userService->haveAnyDocumentApproved()) {
                $userStatus = $user->present()->getAddressDocumentStatus(UserDocument::STATUS_APPROVED);
            }

            if ($userService->haveAnyDocumentRefused()) {
                $userStatus = $user->present()->getAddressDocumentStatus(UserDocument::STATUS_REFUSED);
            }

            $companyStatus = null;
            $companyRedirect = null;

            $companies = Company::where("user_id", auth()->user()->account_owner_id)
                ->where("active_flag", true)
                ->get();

            if (0 === $companies->count()) {
                $companyRedirect = "/companies";
            } else {
                $companyApproved = $companyService->companyDocumentApproved();
                if (!empty($companyApproved)) {
                    $companyStatus = $companyModel->present()->getStatus(CompanyDocument::STATUS_APPROVED);
                    $companyRedirect = "/companies";
                } else {
                    $companyPending = $companyService->companyDocumentPending();
                    if (!empty($companyPending)) {
                        $companyStatus = $companyModel->present()->getStatus(CompanyDocument::STATUS_PENDING);
                        $companyRedirect = "/companies/company-detail/" . hashids_decode($companyPending->id);
                    }

                    $companyAnalyzing = $companyService->companyDocumentAnalyzing();
                    if (!empty($companyAnalyzing)) {
                        $companyStatus = $companyModel->present()->getStatus(CompanyDocument::STATUS_ANALYZING);
                        $companyRedirect = "/companies/company-detail/" . hashids_decode($companyAnalyzing->id);
                    }

                    $companyRefused = $companyService->companyDocumentRefused();
                    if (!empty($companyRefused)) {
                        $companyStatus = $companyModel->present()->getStatus(CompanyDocument::STATUS_REFUSED);
                        $companyRedirect = "/companies/company-detail/" . hashids_decode($companyAnalyzing->id);
                    }
                }
            }

            $this->updateUserStatus($user, $userInformations, $userStatus, $companyStatus);

            return response()->json(
                [
                    "data" => [
                        "informations_completed" => $userInformations,
                        "user_account" => $user->present()->getAccountStatus($user->account_is_approved),
                        "user_status" => $userStatus,
                        "company_status" => $companyStatus,
                        "link_company" => $companyRedirect,
                    ],
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    "message" => $e->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function updateUserStatus($user, $userInformations, $userStatus, $companyStatus): void
    {
        if (!$user->account_is_approved) {
            if ("approved" === $userStatus && "approved" === $companyStatus && $userInformations) {
                $user->update([
                    "account_is_approved" => 1,
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
            $accountType = $user->id === $user->account_owner_id ? "owner" : "collaborator";

            if ($userDocumentRefused) {
                $refused = true;
                $link = "/personal-info";
            } else {
                $companyDocumentRefused = $companyService->companyDocumentRefused();
                $companyDocumentApproved = $companyService->companyDocumentApproved();
                if (empty($companyDocumentApproved) && !empty($companyDocumentRefused)) {
                    $refused = true;
                    $companyCode = hashids_decode($companyDocumentRefused->id);
                    if (
                        $companyDocumentRefused->company_type ===
                        $companyDocumentRefused->present()->getCompanyType("physical person")
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

            if (env("ACCOUNT_FRONT_URL") && empty($link)) {
                $link = env("ACCOUNT_FRONT_URL") . $link;
            }

            return response()->json([
                "message" => "Documentos verificados!",
                "analyzing" => $analyzing,
                "refused" => $refused,
                "link" => $link,
                "accountType" => $accountType,
                "accountStatus" => $userModel->present()->getStatus($user->status),
            ]);
        } catch (Exception $e) {
            report($e);

            return response()->json(["error" => "Erro ao verificar documentos"], 400);
        }
    }

    public function verifyBankAndAddress()
    {
        try {
            $companyService = new CompanyService();

            $link = null;
            $refused = false;

            $companyAddressPending = $companyService->companyAddressPending();

            if (!empty($companyAddressPending)) {
                $refused = true;
                $companyCode = hashids_encode($companyAddressPending->id);
                $link = env("ACCOUNT_FRONT_URL") . "/companies/company-detail/" . $companyCode;
            } else {
                $companyBankAccountPending = $companyService->companyBankAccountPending();

                if (!empty($companyBankAccountPending)) {
                    $refused = true;
                    $companyCode = hashids_encode($companyBankAccountPending->id);
                    $link = env("ACCOUNT_FRONT_URL") . "/companies/company-detail/" . $companyCode;
                }
            }

            return response()->json([
                "message" => "Verificar dados bancários e endereço!",
                "refused" => $refused,
                "link" => $link,
            ]);
        } catch (Exception $e) {
            report($e);

            return response()->json(["error" => "Erro ao verificar documentos"], 400);
        }
    }

    public function companies(Request $request)
    {
        try {
            $companyService = new CompanyService();

            $paginate = true;
            if ($request->has("select") && $request->input("select")) {
                $paginate = false;
            }

            return $companyService->getCompaniesUser($paginate);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro, tente novamente mais tarde",
                ],
                400
            );
        }
    }

    public function getCompanies()
    {
        try {
            $user = auth()->user();

            $return = [
                "company_default" => Hashids::encode(Company::DEMO_ID),
                "company_default_name" => "Empresa Demo",
                "company_default_fullname" => "Empresa Demo",
                "has_api_integration" => false,
            ];

            if ($user->company_default > Company::DEMO_ID) {
                $companyDefault = cache()->remember("company-default-" . $user->company_default, 60, function () use (
                    $user
                ) {
                    return Company::select("id", "company_type", "fantasy_name")
                        ->where("id", $user->company_default)
                        ->first();
                });

                $company_default_name =
                    1 === $companyDefault->company_type
                        ? "Pessoa física"
                        : Str::limit($companyDefault->fantasy_name, 20) ?? "";

                $user_id = auth()->user()->id;
                if (auth()->user()->is_cloudfox) {
                    $user_id = auth()->user()->logged_id;
                }

                $return = [
                    "company_default" => Hashids::encode($user->company_default),
                    "company_default_name" => $company_default_name,
                    "company_default_fullname" => $companyDefault->fantasy_name,
                    "has_api_integration" => false,
                ];
            }

            $companies = cache()->remember(
                "companies-" . $user->account_owner_id, 60,
                fn() => Company::where("user_id", $user->account_owner_id)->get()
            );

            $return["companies"] = collect(CompaniesSelectResource::collection($companies))
                ->sortBy("order_priority")
                ->sortByDesc("active_flag")
                ->sortByDesc("company_is_approved")
                ->values()
                ->all();

            return $return;
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao tentar buscar dados, tente novamente mais tarde",
                ],
                400
            );
        }
    }

    public function updateCompanyDefault(Request $request)
    {
        if (empty($request->company_id)) {
            return response()->json(["message" => "Informe a empresa selecionada"], 400);
        }

        $companyId = current(Hashids::decode($request->company_id));
        if (empty($companyId)) {
            return response()->json(["message" => "Não foi possivel identificar a empresa"], 400);
        }

        $user = Auth::user();
        if ($user->company_default === $companyId) {
            return; //response()->json(['message'=>'A empresa selecionada já é a default.'],400);
        }

        if ($companyId > 1) {
            $company = Company::where("user_id", $user->account_owner_id)
                ->where("id", $companyId)
                ->exists();
            if (empty($company)) {
                return response()->json(["message" => "Não foi possivel identificar a empresa"], 400);
            }
        }

        try {
            $user->company_default = $companyId;
            $user->save();

            return response()->json(["message" => "Empresa atualizada."]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Não foi possivel atualizar a empresa default."]);
        }
    }

    public function allowBlockBalance($companyId, $saleId)
    {
        try {
            $company = Company::find(hashids_decode($companyId));
            $sale = Sale::find(hashids_decode($saleId, "sale_id"));

            if (empty($company) || empty($sale)) {
                return response()->json("Dados inválidos", 400);
            }

            $safe2payService = new Safe2PayService();
            $safe2payService->setCompany($company);

            $availableBalance = $safe2payService->getAvailableBalance();
            $pendingBalance = $safe2payService->getPendingBalance();

            (new CompanyService())->applyBlockedBalance($safe2payService, $availableBalance, $pendingBalance);

            $transaction = Transaction::where("sale_id", $sale->id)
                ->where("company_id", $company->id)
                ->first();

            $response = (object)[
                "allow_block" => $availableBalance + $pendingBalance >= $transaction->value,
            ];

            return response()->json($response);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao verificar bloqueio da comissão!"], 400);
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

            return response()->json(["message" => "Sucesso"]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao notificar alteração no chamado!"], 400);
        }
    }

    public function getBonusBalance()
    {
        $bonusBalance = BonusBalance::where("user_id", auth()->user()->account_owner_id)
            ->where("expires_at", ">=", today())
            ->where("current_value", ">", 0)
            ->first();

        if (empty($bonusBalance)) {
            return response()->json([
                "error" => "bonus balance not found",
            ]);
        }

        return response()->json([
            "user_name" => auth()
                ->user()
                ->present()
                ->firstName(),
            "total_bonus" => foxutils()->formatMoney($bonusBalance->total_value / 100),
            "current_bonus" => foxutils()->formatMoney($bonusBalance->current_value / 100),
            "used_bonus" => foxutils()->formatMoney(($bonusBalance->total_value - $bonusBalance->current_value) / 100),
            "expires_at" => Carbon::parse($bonusBalance->expires_at)->format("d/m/Y"),
            "used_percentage" => floor(100 - ($bonusBalance->current_value * 100) / $bonusBalance->total_value),
        ]);
    }

    public function checkDocumentOnBureau(string $userId)
    {
        try {
            $user = User::findOrFail(hashids_decode($userId));
            event(new UserRegistrationFinishedEvent($user));
            return response("", Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            report($e);
            return response("User not found.", 404);
        } catch (Exception $e) {
            report($e);
            return response("Internal server error.", 500);
        }
    }

    public function getZendeskToken()
    {
        $payload = [
            "scope" => "user",
            "name" => auth()->user()->name,
            "email" => auth()->user()->email,
            "external_id" => "" . auth()->user()->id . "",
            "iat" => time(),
        ];
        $token = JWT::encode(
            $payload,
            "v5D7n6jaGlc2nviUtU5eYOuG9MmtIuJ_t9K8KERl5PK6a46sWNH6q5_28jsGaTU1I4eStyGmzDOUntuhdHfoGg",
            "HS256",
            "app_630519303703d200f36b2a98"
        );

        return response()->json($token);
    }

    public function verifyBiometry($id)
    {
        try {
            $user = User::find(hashids_decode($id));
            $accountOwner = User::find($user->account_owner_id);

            return response()->json(
                [
                    "data" => [
                        "check_user_biometry" => !UserBiometryStatusEnum::isApproved($accountOwner->biometry_status),
                    ],
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    "message" => $e->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
