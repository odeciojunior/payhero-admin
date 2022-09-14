<?php

declare(strict_types=1);

namespace Modules\Api\Validators\V1;

class SalesRule
{
    protected static $rules = [
        "transaction" => "sometimes|required|string",
        "company" => "sometimes|required|string",
        "user" => "sometimes|required|string",
        "status" => "sometimes|required|string|in:approved,pending,charge_back,refunded,partial_refunded,in_review,canceled_antifraud,in_dispute",
        "date_type" => "sometimes|required|string|in:start_date,end_date",
        "date_range" => "sometimes|required|string",
        "page" => "sometimes|required|integer",
    ];

    public static function getSalesRules()
    {
        return [
            "transaction" => self::$rules["transaction"],
            "company" => self::$rules["company"],
            "user" => self::$rules["user"],
            "status" => self::$rules["status"],
            "date_type" => self::$rules["date_type"],
            "date_range" => self::$rules["date_range"],
            "page" => self::$rules["page"],
        ];
    }

    public static function messages()
    {
        return [

        ];
    }
}
