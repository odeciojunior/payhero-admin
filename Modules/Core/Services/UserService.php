<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Core\DataTransferObjects\BureauUserDataInterface;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\PromotionalTax;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Enums\User\UserBiometryStatusEnum;
use Symfony\Component\HttpFoundation\Response;

final class UserService
{
    private BigBoostService $bureauService;

    public function __construct()
    {
        $this->bureauService = new BigBoostService();
    }

    public function isDocumentValidated($userId = null): bool
    {
        if (empty($userId)) {
            $user = auth()->user();
            if ($user->is_cloudfox && $user->logged_id) {
                $user = User::find($user->account_owner_id);
            }
        } else {
            $user = User::find($userId);
        }

        if (!empty($user)) {
            return User::DOCUMENT_STATUS_APPROVED === $user->address_document_status &&
                UserBiometryStatusEnum::isApproved($user->biometry_status);
        }

        return false;
    }

    public function haveAnyDocumentApproved(): bool
    {
        $user = User::query()->find(auth()->user()->account_owner_id);

        return UserDocument::STATUS_APPROVED === $user->address_document_status &&
            UserBiometryStatusEnum::isApproved($user->biometry_status);
    }

    public function haveAnyDocumentPending(): bool
    {
        $user = User::find(auth()->user()->account_owner_id);

        return UserDocument::STATUS_PENDING === $user->address_document_status ||
            $user->biometry_status === UserBiometryStatusEnum::PENDING->value;
    }

    public function haveAnyDocumentAnalyzing(): bool
    {
        $user = User::find(auth()->user()->account_owner_id);

        return UserDocument::STATUS_ANALYZING === $user->address_document_status ||
            UserBiometryStatusEnum::IN_PROCESS->value === $user->biometry_status;
    }

    public function haveAnyDocumentRefused(): bool
    {
        $user = User::find(auth()->user()->account_owner_id);

        return UserDocument::STATUS_REFUSED === $user->address_document_status ||
            UserBiometryStatusEnum::REFUSED->value === $user->biometry_status;
    }

    public function verifyCpf($cpf): bool
    {
        $userModel = new User();
        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        $userPresenter = $userModel->present();

        $user = $userModel
            ->where([
                ["document", $cpf],
                ["address_document_status", $userPresenter->getAddressDocumentStatus("approved")],
                ["personal_document_status", $userPresenter->getPersonalDocumentStatus("approved")],
            ])
            ->first();
        if (!empty($user)) {
            return true;
        }

        return false;
    }

    public function verifyFieldsEmpty(User $user): bool
    {
        if (empty($user->email)) {
            return true;
        }
        if (empty($user->cellphone)) {
            return true;
        }
        if (empty($user->document)) {
            return true;
        }
        if (empty($user->zip_code)) {
            return true;
        }
        if (empty($user->country)) {
            return true;
        }
        if (empty($user->state)) {
            return true;
        }
        if (empty($user->city)) {
            return true;
        }
        if (empty($user->neighborhood)) {
            return true;
        }
        if (empty($user->street)) {
            return true;
        }
        if (empty($user->number)) {
            return true;
        }

        return false;
    }

    public function verifyIsValidCPF($cpf): bool
    {
        $cpf = preg_replace("/[^0-9]/is", "", $cpf);

        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * ($t + 1 - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    public function verifyExistsCPF($cpf): bool
    {
        $userModel = new User();
        $cpf = preg_replace("/[^0-9]/", "", $cpf);

        $user = $userModel->where("document", $cpf)->first();
        if (!empty($user)) {
            return true;
        }

        return false;
    }

    public function getUserByIdwallCPF($cpf)
    {
        try {
            $idewallService = new IdwallService();
            $userStatus = json_decode($idewallService->getGenerateProtocolByCPF($cpf), true);
            $userProtocol = $userStatus["result"]["numero"];

            /**
             * SLEEP É NECESSÁRIO PARA TER TEMPO DE PROCESSAR O RELATÓRIO
             */
            sleep(3);

            if (!empty($userProtocol) && Response::HTTP_OK === $userStatus["status_code"]) {
                $user = $idewallService->getReportByProtocolNumber($userProtocol);

                return json_decode($user, true);
            }

            return false;
        } catch (Exception) {
            return false;
        }
    }

    public function userWithdrawalBlocked($user): bool
    {
        return $user->status === (new User())->present()->getStatus("withdrawal blocked");
    }

    public function removePromotionalTax(PromotionalTax $promotionalTax): void
    {
        try {
            DB::beginTransaction();
            $old_taxes = explode(",", $promotionalTax->old_tax);

            foreach ($old_taxes as $old_tax) {
                if (empty($old_tax)) {
                    continue;
                }

                $res = explode(":", $old_tax);
                $company_id = $res[0];
                $tax = $res[1];
                $company = Company::where("user_id", $promotionalTax->user_id)
                    ->where("id", $company_id)
                    ->first();

                if (PromotionalTax::PROMOTIONAL_TAX === $tax) {
                    $tax = (new CompanyService())->getTax($company->credit_card_tax);
                }

                if ($company) {
                    $company->gateway_tax = $tax;
                    $company->save();
                }
            }

            $promotionalTax->delete();
            DB::commit();
        } catch (Exception $e) {
            report($e);
            DB::rollback();
        }
    }

    public function addExpirationDatePromotionalTax(PromotionalTax $promotionalTax): void
    {
        try {
            DB::beginTransaction();

            $user = $promotionalTax->user;
            $old_tax = "";

            $date = Carbon::parse($promotionalTax->created_at);
            $hasSale = (new SaleService())->verifyIfUserHasSalesByDate($date, $user->id);

            if ($hasSale) {
                foreach ($user->companies as $company) {
                    $old_tax .= $company->id . ":" . $company->gateway_tax . ",";
                    $company->gateway_tax = PromotionalTax::PROMOTIONAL_TAX;
                    $company->update();
                }

                PromotionalTax::updateOrCreate(
                    ["user_id" => $user->id],
                    [
                        "tax" => PromotionalTax::PROMOTIONAL_TAX,
                        "old_tax" => $old_tax,
                        "expiration" => Carbon::today()->addDays(30),
                        "active" => true,
                    ]
                );
            }

            DB::commit();
        } catch (Exception $e) {
            report($e);
            DB::rollback();
        }
    }

    /**
     * @param $cpf
     * @return BureauUserDataInterface
     * @throws Exception
     */
    public function getBureauUserData($cpf): BureauUserDataInterface
    {
        try {
            if (FoxUtils::isEmpty($cpf)) {
                throw new InvalidArgumentException("Trying to query an Invalid CPF on BigId");
            }
            return $this->bureauService->getUserData($cpf);
        } catch (Exception $e) {
            report($e);
            throw $e;
        }
    }

    public function updateUserDataFromBureau($cpf): void
    {
        try {
            /** @var User $user */
            $user = User::whereDocument($cpf)->firstOrFail();
            $bureauUserData = $this->getBureauUserData($cpf);
            $user->bureau_result = json_encode($bureauUserData->getRawData());
            $user->name = $bureauUserData->getName() ?: $user->name;
            $user->date_birth = $bureauUserData->getBirthDate()
                ? $bureauUserData->getBirthDate()->format("Y-m-d")
                : $user->date_birth;
            $user->mother_name = $bureauUserData->getMotherName() ?: $user->mother_name;
            $user->bureau_check_count += 1;
            $user->bureau_data_updated_at = now();
            if ($bureauUserData->isAbleToCreateAccount()) {
                $user->observation = str_replace("CPF não encontrado ou inválido ()", "", $user->observation);
                $user->observation = FoxUtils::isEmpty($user->observation) ? null : $user->observation;
            } else {
                $user->observation = $bureauUserData->getIssues();
                $user->observation .=
                    ($user->observation ? " " : "") .
                    ($user->bureau_check_count ?: 1) .
                    "ª Tentativa de revalidação do CPF no bureau em " .
                    Carbon::now()->format("d/m/Y à\s H:i:s");
            }
            $user->save();
        } catch (Exception $e) {
            report($e);
        }
    }
}
