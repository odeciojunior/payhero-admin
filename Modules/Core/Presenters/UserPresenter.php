<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

/**
 * Class UserPresenter
 * @package Modules\Core\Presenters
 */
class UserPresenter extends Presenter
{
    const USUARIO_DOCUMENTO = 1;
    const USUARIO_RESIDENCIA = 2;
    const USUARIO_CPF = 3;
    const USUARIO_EXTRATO = 5;
    const EMPRESA_EXTRATO = 1;
    const EMPRESA_RESINDENCIA = 2;
    const EMPRESA_CCMEI = 3;
    /**
     * @return string
     */
    public function getTransactionRate()
    {
        return number_format($this->transaction_rate, 2, ',', '.');
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
                    return 'active';
                case 2:
                    return 'withdrawal blocked';
                case 3:
                    return 'account blocked';
            }

            return '';
        } else {
            switch ($status) {
                case 'active':
                    return 1;
                case 'withdrawal blocked':
                    return 2;
                case 'account blocked':
                    return 3;
            }

            return '';
        }
    }

    /**
     * @param $status
     * @return int|string
     */
    public function getAddressDocumentStatus($status)
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
    public function getPersonalDocumentStatus($status)
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
                    return 'personal_document';
                case 2:
                    return 'address_document';
            }

            return '';
        } else {
            switch ($type) {
                case 'personal_document':
                    return 1;
                case 'address_document':
                    return 2;
            }

            return '';
        }
    }

    /**
     * @param $document_type
     * @return int|string
     */
    public function getDocumentTypeRegistered($document_type)
    {
        switch ($document_type) {
            //USUARIO
            case 'USUARIO_RESIDENCIA':
                return self::USUARIO_RESIDENCIA;
            case 'USUARIO_DOCUMENTO':
                return self::USUARIO_DOCUMENTO;
            case 'USUARIO_CPF':
                return self::USUARIO_CPF;
            case 'USUARIO_EXTRATO':
                return self::USUARIO_EXTRATO;

            //EMPRESA
            case 'EMPRESA_EXTRATO':
                return self::EMPRESA_EXTRATO;
            case 'EMPRESA_RESIDENCIA':
                return self::EMPRESA_RESINDENCIA;
            case 'EMPRESA_CCMEI':
                return self::EMPRESA_CCMEI;
        }
            return '';
    }
}
