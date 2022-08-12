<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class TicketMessagePresenter extends Presenter
{
    public function getType($type = 0)
    {
        if (!$type) {
            $type = $this->type_enum;
        }
        $typesArray = [
            1 => "from_customer",
            2 => "from_admin",
            3 => "from_system",
        ];
        return is_numeric($type) ? $typesArray[$type] ?? "" : array_search($type, $typesArray) ?? "";
    }
}
