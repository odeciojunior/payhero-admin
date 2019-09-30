<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class UserPresenter extends Presenter
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

    public function getPersonalDocumentStatus($status) {

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

    public function getDocumentType($type){

        if(is_numeric($type)){
            switch ($type) {
                case 1:
                    return 'personal_document';
                case 2:
                    return 'address_document';
            }
            return '';
        }
        else{
            switch ($type) {
                case 'personal_document':
                    return 1;
                case 'address_document':
                    return 2;
            }
            return '';
        }
    }

}
