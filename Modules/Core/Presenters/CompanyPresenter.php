<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\FoxUtils;

class CompanyPresenter extends Presenter
{
    protected $entity;

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

    public function getBankDocumentStatus($bankStatus = null)
    {
        /** @var Company $company */
        $company = $this->entity;
        $status = $bankStatus ?? $company->bank_document_status;
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
        }

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
        }

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

    public function getDocumentType($type)
    {
        if (is_numeric($type)) {
            switch ($type) {
                // case 1:
                //     return 'bank_document_status';
                case 2:
                    return 'address_document_status';
                case 3:
                    return 'contract_document_status';
            }

            return '';
        } 
        
        switch ($type) {
            // case 'bank_document_status':
            //     return 1;
            case 'address_document_status':
                return 2;
            case 'contract_document_status':
                return 3;
        }

        return '';
        
    }

    public function getStatus($status)
    {
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
        }

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
        }

        switch ($status) {
            case 'physical person':
                return 1;
            case 'juridical person':
                return 2;
        }

        return '';
        
    }

    public function allStatusPending()
    {
        return $this->entity->bank_document_status == 3 &&
            $this->entity->address_document_status == 3 &&
            $this->entity->contract_document_status == 3;
    }

    /*
    public function getAccountType($type = null)
    {
        $company = $this->entity;
        $status = $type ?? $company->account_type;
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'C';
                case 2:
                    return 'P';
            }

            return '';
        } 

        switch ($status) {
            case 'C':
                return 1;
            case 'P':
                return 2;
        }

        return '';        
    }*/

    public function getStatusGetnet($status = null)
    {
        $company = $this->entity;
        $status = $status ?? $company->getGatewayStatus(Gateway::GETNET_PRODUCTION_ID);
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'approved';
                case 2:
                    return 'review';
                case 3:
                    return 'reproved';
                case 4:
                    return 'approved_getnet';
                case 5:
                    return 'error';
                case 6:
                    return 'pending';
            }

            return '';
        }
        
        switch ($status) {
            case 'approved':
                return 1;
            case 'review':
                return 2;
            case 'reproved':
                return 3;
            case 'approved_getnet' :
                return 4;
            case 'error' :
                return 5;
            case 'pending' :
                return 6;
        }

        return '';
        
    }

}
