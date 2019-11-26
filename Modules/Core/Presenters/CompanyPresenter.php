<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;
use Modules\Core\Entities\Company;

/**
 * Class CompanyPresenter
 * @package Modules\Core\Presenters
 */
class CompanyPresenter extends Presenter
{
    /**
     * @var Company
     */
    protected $entity;

    /**
     * @param int|string $addressDocumentStatus
     * @return int|string
     */
    public function getAddressDocumentStatus($addressDocumentStatus = null)
    {
        $status = $addressDocumentStatus ?? $this->entity->address_document_status;
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'pending';
                case 2:
                    return 'analyzing';
                case 3:
                    return 'approved';
                case 4:
                    return 'refused';
            }

            return '';
        } else {
            switch ($status) {
                case 'pending':
                    return 1;
                case 'analyzing':
                    return 2;
                case 'approved':
                    return 3;
                case 'refused':
                    return 4;
            }

            return '';
        }
    }

    /**
     * @param int|string $bankStatus
     * @return int|string
     */
    public function getBankDocumentStatus($bankStatus = null)
    {
        /** @var Company $company */
        $company = $this->entity;
        $status  = $bankStatus ?? $company->bank_document_status;
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'pending';
                case 2:
                    return 'analyzing';
                case 3:
                    return 'approved';
                case 4:
                    return 'refused';
            }

            return '';
        } else {
            switch ($status) {
                case 'pending':
                    return 1;
                case 'analyzing':
                    return 2;
                case 'approved':
                    return 3;
                case 'refused':
                    return 4;
            }

            return '';
        }
    }

    /**
     * @param int|string $contractDocumentStatus
     * @return int|string
     */
    public function getContractDocumentStatus($contractDocumentStatus = null)
    {
        $status = $contractDocumentStatus ?? $this->entity->contract_document_status;
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'pending';
                case 2:
                    return 'analyzing';
                case 3:
                    return 'approved';
                case 4:
                    return 'refused';
            }

            return '';
        } else {
            switch ($status) {
                case 'pending':
                    return 1;
                case 'analyzing':
                    return 2;
                case 'approved':
                    return 3;
                case 'refused':
                    return 4;
            }

            return '';
        }
    }

    /**
     * @param $type
     * @return int|string
     */
    public function getDocumentType($type)
    {
        if (is_numeric($type)) {
            switch ($type) {
                case 1:
                    return 'bank_document_status';
                case 2:
                    return 'address_document_status';
                case 3:
                    return 'contract_document_status';
            }

            return '';
        } else {
            switch ($type) {
                case 'bank_document_status':
                    return 1;
                case 'address_document_status':
                    return 2;
                case 'contract_document_status':
                    return 3;
            }

            return '';
        }
    }

    /**
     * @param $status
     * @return int|string
     */
    public function getStatus($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'pendente';
                case 2:
                    return 'analyzing';
                case 3:
                    return 'aprovado';
                case 4:
                    return 'refused';
            }

            return '';
        } else {
            switch ($status) {
                case 'pending':
                    return 1;
                case 'analyzing':
                    return 2;
                case 'approved':
                    return 3;
                case 'refused':
                    return 4;
            }

            return '';
        }
    }

    /**
     * @param $status
     * @return int|string
     */
    public function getCompanyType($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'physical person';
                case 2:
                    return 'juridical person';
            }

            return '';
        } else {
            switch ($status) {
                case 'physical person':
                    return 1;
                case 'juridical person':
                    return 2;
            }

            return '';
        }
    }
    /**
     * @return bool
     */
    public function allStatusPending()
    {
        return $this->entity->bank_document_status == 3 &&
            $this->entity->address_document_status == 3 &&
            $this->entity->contract_document_status == 3;
    }
}
