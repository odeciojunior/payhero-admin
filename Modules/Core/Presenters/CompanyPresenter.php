<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class CompanyPresenter extends Presenter
{
    public function getAddressDocumentStatus($status) {

        if(is_numeric($status)){
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
        else{
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

    public function getBankDocumentStatus($status) {

        if(is_numeric($status)){
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
        else{
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

    public function getContractDocumentStatus($status) {

        if(is_numeric($status)){
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
        else{
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

    public function getStatus($status) {

        if(is_numeric($status)){
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
        }
        else{
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

    public function getDocumentType($type){

        if(is_numeric($type)){
            switch ($type) {
                case 1:
                    return 'bank_document_status';
                case 2:
                    return 'address_document_status';
                case 3: 
                    return 'contract_document_status';
            }
            return '';
        }
        else{
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

}
