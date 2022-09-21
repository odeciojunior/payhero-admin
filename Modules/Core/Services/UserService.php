<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Core\DataTransferObjects\BureauUserDataInterface;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\PromotionalTax;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;

class UserService
{
    private $bureauService;

    public function __construct()
    {
        $this->bureauService = new BigBoostService();
    }

    public function isDocumentValidated($userId = null): bool
    {
        $userModel = new User();
        if (empty($userId)) {
            $user = auth()->user();
        } else {
            $user = User::find($userId);
        }

        $userPresenter = $userModel->present();
        if (!empty($user)) {
            return $user->address_document_status == User::DOCUMENT_STATUS_APPROVED &&
                $user->personal_document_status == User::DOCUMENT_STATUS_APPROVED;
        }

        return false;
    }

    public function haveAnyDocumentApproved(): bool
    {
        $user = User::find(auth()->user()->account_owner_id);

        if (
            $user->address_document_status == UserDocument::STATUS_APPROVED &&
            $user->biometry_status == User::BIOMETRY_STATUS_APPROVED
        ) {
            return true;
        }

        return false;
    }

    public function haveAnyDocumentPending(): bool
    {
        $user = User::find(auth()->user()->account_owner_id);

        if (
            $user->address_document_status == UserDocument::STATUS_PENDING 
        ) {
            return true;
        }

        return false;
    }

    public function haveAnyDocumentAnalyzing(): bool
    {
        $user = User::find(auth()->user()->account_owner_id);

        if (
            $user->address_document_status == UserDocument::STATUS_ANALYZING ||
            $user->biometry_status == User::BIOMETRY_STATUS_IN_PROCESS
        ) {
            return true;
        }

        return false;
    }

    public function haveAnyDocumentRefused(): bool
    {
        $user = User::find(auth()->user()->account_owner_id);

        if (
            $user->address_document_status == UserDocument::STATUS_REFUSED ||
            $user->biometry_status == User::BIOMETRY_STATUS_REFUSED
        ) {
            return true;
        }

        return false;
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
        } elseif (empty($user->cellphone)) {
            return true;
        } elseif (empty($user->document)) {
            return true;
        } elseif (empty($user->zip_code)) {
            return true;
        } elseif (empty($user->country)) {
            return true;
        } elseif (empty($user->state)) {
            return true;
        } elseif (empty($user->city)) {
            return true;
        } elseif (empty($user->neighborhood)) {
            return true;
        } elseif (empty($user->street)) {
            return true;
        } elseif (empty($user->number)) {
            return true;
        } else {
            return false;
        }
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

            if (!empty($userProtocol) && $userStatus["status_code"] == 200) {
                $user = $idewallService->getReportByProtocolNumber($userProtocol);

                return json_decode($user, true);
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function userWithdrawalBlocked($user): bool
    {
        if ($user->status == (new User())->present()->getStatus("withdrawal blocked")) {
            return true;
        }

        return false;
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

                if ($tax == PromotionalTax::PROMOTIONAL_TAX) {
                    $tax = (new CompanyService())->getTax($company->gateway_release_money_days);
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
     * @return false|mixed
     */
    public function getBureauUserData($cpf): BureauUserDataInterface
    {
        try {
            if (FoxUtils::isEmpty($cpf)) {
                throw new \InvalidArgumentException("Trying to query an Invalid CPF on BigId");
            }
            return $this->bureauService->getUserData($cpf);
        } catch (\Exception $e) {
            report($e);
            throw $e;
        }
    }

    public function updateUserDataFromBureau($cpf)
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
        } catch (\Exception $e) {
            report($e);
        }
    }
}
