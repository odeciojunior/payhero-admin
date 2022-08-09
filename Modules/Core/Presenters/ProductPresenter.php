<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

/**
 * Class NotazzInvoicePresenter
 * @package Modules\Core\Presenters
 */
class ProductPresenter extends Presenter
{
    /**
     * @param $currency
     * @return int|string
     */
    public function getCurrency($currency)
    {
        if (is_numeric($currency)) {
            switch ($currency) {
                case 1:
                    return "BRL";
                case 2:
                    return "USD";
            }

            return "";
        } else {
            switch ($currency) {
                case "BRL":
                    return 1;
                case "USD":
                    return 2;
            }

            return "";
        }
    }

    /**
     * @param $type
     * @return int|string
     */
    public function getType($type)
    {
        if (is_numeric($type)) {
            switch ($type) {
                case 1:
                    return "physical";
                case 2:
                    return "digital";
            }

            return "";
        } else {
            switch ($type) {
                case "physical":
                    return 1;
                case "digital":
                    return 2;
            }

            return "";
        }
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
                    return "analyzing";
                case 2:
                    return "approved";
                case 3:
                    return "refused";
            }

            return "";
        } else {
            switch ($status) {
                case "analyzing":
                    return 1;
                case "approved":
                    return 2;
                case "refused":
                    return 3;
            }

            return "";
        }
    }
}
