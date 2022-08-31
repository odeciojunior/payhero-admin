<?php

namespace Modules\Core\DataTransferObjects;

interface BureauUserDataInterface
{
    public function getRawData(): array;

    public function getCPF(): string;

    public function getName(): string;

    public function getMotherName(): ?string;

    public function getBirthDate(): ?\DateTime;

    public function hasObitIndication(): bool;

    public function isUnderAgePerson(): bool;

    public function isTaxIdActive(): bool;

    public function isAbleToCreateAccount(): bool;

    public function getIssues(): string;
}
