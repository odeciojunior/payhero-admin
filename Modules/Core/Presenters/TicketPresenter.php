<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;
use Modules\Core\Entities\TicketMessage;

class TicketPresenter extends Presenter
{
    public function getTicketCategoryEnum($category = 0)
    {
        if (!$category) {
            $category = $this->ticket_category_enum;
        }

        $categoryArray = [
            1 => "complaint",
            2 => "doubt",
            3 => "suggestion",
        ];

        return (is_numeric($category) ? $categoryArray[$category] : array_search($category, $categoryArray)) ?? "";
    }

    public function getSubjectEnum($subject = 0)
    {
        if (!$subject) {
            $subject = $this->ticket_category_enum;
        }

        $subjectArray = [
            1 => "differs_from_advertised",
            2 => "damaged_by_transport",
            3 => "manufacturing_defect",
            4 => "tracking_code_not_received",
            5 => "non_trackable_order",
            6 => "delivery_delay",
            7 => "delivery_to_wrong_address",
            8 => "others",
        ];

        return (is_numeric($subject) ? $subjectArray[$subject] : array_search($subject, $subjectArray)) ?? "";
    }

    public function getTicketStatusEnum($status = 0)
    {
        if (!$status) {
            $status = $this->ticket_status_enum;
        }

        $statusArray = [
            1 => "open",
            2 => "closed",
            3 => "mediation",
        ];

        return (is_numeric($status) ? $statusArray[$status] : array_search($status, $statusArray)) ?? "";
    }

    public function getLastMessageType($type = 0)
    {
        if (!$type) {
            $type = $this->last_message_type_enum;
        }
        return (new TicketMessage())->present()->getType($type);
    }
}
