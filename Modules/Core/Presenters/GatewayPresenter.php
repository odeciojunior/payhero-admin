<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

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
}
