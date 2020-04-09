<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class TicketPresenter extends Presenter
{
    public function getTicketCategoryEnum($category = 0)
    {
        if(!$category) $category = $this->ticket_category_enum;

        if (is_numeric($category)) {
            switch ($category) {
                case 1:
                    return 'complaint';
                case 2:
                    return 'doubt';
                case 3:
                    return 'suggestion';
                default:
                    return '';
            }
        } else {
            switch ($category) {
                case 'complaint':
                    return 1;
                case 'doubt':
                    return 2;
                case 'suggestion':
                    return 3;
                default:
                    return 0;
            }
        }
    }

    public function getTicketStatusEnum($status = 0)
    {
        if(!$status) $status = $this->ticket_status_enum;

        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'open';
                case 2:
                    return 'closed';
                case 3:
                    return 'mediation';
                default:
                    return '';
            }
        } else {
            switch ($status) {
                case 'open':
                    return 1;
                case 'closed':
                    return 2;
                case 'mediation':
                    return 3;
                default:
                    return 0;
            }
        }
    }
}
