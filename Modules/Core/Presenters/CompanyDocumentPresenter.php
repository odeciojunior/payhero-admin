<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class CompanyDocumentPresenter extends Presenter
{
    public function getTypeEnum($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return "pending";
                case 2:
                    return "analyzing";
                case 3:
                    return "approved";
                case 4:
                    return "refused";
            }

            return "";
        } else {
            switch ($status) {
                case "pending":
                    return 1;
                case "analyzing":
                    return 2;
                case "approved":
                    return 3;
                case "refused":
                    return 4;
            }

            return "";
        }
    }

    public function getDocumentType($type)
    {
        if (is_numeric($type)) {
            switch ($type) {
                case 2:
                    return "address_document_status";
                case 3:
                    return "contract_document_status";
            }
        } else {
            switch ($type) {
                case "address_document_status":
                    return 2;
                case "contract_document_status":
                    return 3;
            }
        }

        return "";
    }
}
