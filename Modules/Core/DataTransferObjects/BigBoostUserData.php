<?php

namespace Modules\Core\DataTransferObjects;

class BigBoostUserData implements BureauUserDataInterface
{
    private $rawData;

    public function __construct($data)
    {
        $this->rawData = $data;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function getCPF(): string
    {
        return $this->rawData["Result"][0]["BasicData"]["TaxIdNumber"] ?? "";
    }

    public function getName(): string
    {
        return $this->rawData["Result"][0]["BasicData"]["Name"] ?? "";
    }

    public function isTaxIdActive(): bool
    {
        $statusMessage = $this->rawData["Result"][0]["BasicData"]["TaxIdStatus"] ?? "";
        $activeStatus = "REGULAR";

        return $statusMessage == $activeStatus;
    }

    public function hasObitIndication(): bool
    {
        $basicData = $this->rawData["Result"][0]["BasicData"] ?? [];

        return $basicData["HasObitIndication"] ?? false;
    }

    public function isUnderAgePerson(): bool
    {
        if (!isset($this->rawData["Status"])) {
            return false;
        }
        $status = $this->rawData["Status"];
        if (!isset($status["date_of_birth_validation"])) {
            return false;
        }
        $dateValidation = $status["date_of_birth_validation"];
        $defaultErrorMessage = "THIS CPF BELONGS TO A MINOR. DATE OF BIRTH IS NEEDED TO PROCESS REQUEST.";
        $code = $dateValidation[0]["Code"];
        $message = $dateValidation[0]["Message"] ?? "";

        return $code === -200 && $message === $defaultErrorMessage;
    }

    public function isAbleToCreateAccount(): bool
    {
        return $this->isTaxIdActive() && !$this->hasObitIndication() && !$this->isUnderAgePerson();
    }

    public function getIssues(): string
    {
        $issues = [];
        if (!$this->isTaxIdActive()) {
            $issues[] = "CPF não encontrado ou inválido (" . $this->getStatusMessage() . ")";
        }
        if ($this->isUnderAgePerson()) {
            $issues[] = "Menor de idade";
        }
        if ($this->hasObitIndication()) {
            $issues[] = "Indicação de óbito";
        }

        return implode(",", $issues);
    }

    private function getStatusMessage(): string
    {
        return $this->rawData["Result"][0]["BasicData"]["TaxIdStatus"] ?? "";
    }
}
