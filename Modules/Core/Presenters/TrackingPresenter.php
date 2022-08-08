<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class TrackingPresenter extends Presenter
{
    public function getTrackingStatusEnum($status)
    {
        $statusArray = [
            1 => "posted",
            2 => "dispatched",
            3 => "delivered",
            4 => "out_for_delivery",
            5 => "exception",
        ];

        return (is_numeric($status) ? $statusArray[$status] : array_search($status, $statusArray)) ?? "";
    }

    public function getSystemStatusEnum($status)
    {
        $statusArray = [
            1 => "valid", // O código passou em todas as validações
            2 => "no_tracking_info", // O código é reconhecido pela transportadora mas ainda não tem nenhuma movimentação
            3 => "unknown_carrier", // O código não foi reconhecido por nenhuma transportadora
            4 => "posted_before_sale", // A data de postagem da remessa é anterior a data da venda
            5 => "duplicated", // Já existe uma venda com esse código de rastreio cadastrado
            7 => "checked_manually", // Código de rastreio verificado manualmente (no Manager)
        ];

        return (is_numeric($status) ? $statusArray[$status] : array_search($status, $statusArray)) ?? "";
    }
}
