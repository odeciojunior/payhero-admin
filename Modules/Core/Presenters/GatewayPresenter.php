<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;
use Modules\Core\Entities\Gateway;

/**
 * Class GatewayPresenter
 * @package Modules\Core\Presenters
 */
class GatewayPresenter extends Presenter
{
    public function gatewayEnum($enum)
    {

        if (is_numeric($enum)) {
            switch ($enum) {
                case 1:
                    return 'pagarme_production';
                case 2:
                    return 'pagarme_sandbox';
                case 3:
                    return 'zoop_production';
                case 4:
                    return 'zoop_sandbox';
                case 5:
                    return 'cielo_sandbox';
                case 6:
                    return 'cielo_production';
            }

            return '';
        } else {
            switch ($enum) {
                case 'pagarme_production':
                    return 1;
                case 'pagarme_test':
                    return 2;
                case 'zoop_production':
                    return 3;
                case 'zoop_test':
                    return 4;
            }

            return '';
        }
    }

    public function getName()
    {
        if(in_array($this->id,  [Gateway::GETNET_PRODUCTION_ID, Gateway::GETNET_SANDBOX_ID])) {
            return 'Getnet';
        }
        elseif(in_array($this->id,  [Gateway::ASAAS_PRODUCTION_ID, Gateway::ASAAS_SANDBOX_ID])) {
            return 'Asaas';
        }
        elseif(in_array($this->id,  [Gateway::GERENCIANET_PRODUCTION_ID, Gateway::GERENCIANET_SANDBOX_ID])) {
            return 'Gerencianet';
        }
        elseif(in_array($this->id,  [Gateway::SAFE2PAY_PRODUCTION_ID, Gateway::SAFE2PAY_SANDBOX_ID])) {
            return 'Safe2pay';
        }
        else {
            return 'Cielo';
        }
    }

}
