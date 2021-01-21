<?php

namespace Modules\Getnet\Models;

use Illuminate\Database\Eloquent\Model;

class StatementCommission extends Model
{
    public $fillable = [

        'statement_search_id',

        'type_register',
        'bank',
        'agency',
        'account_number',
        'account_type',
        'marketplace_schedule_id',
        'marketplace_subsellerid',
        'nu_liquid',
        'marketplace_transaction_id',
        'transaction_date',
        'confirmation_date',
        'item_id',
        'number_installments',
        'installment',
        'installment_date',
        'installment_amount',
        'subseller_rate_amount',
        'subseller_rate_percentage',
        'mdr_rate_ammount',
        'mdr_rate_percentage',
        'payment_date',
        'subseller_rate_closing_date',
        'subseller_rate_confirm_date',
        'cancel_custom_key',
        'cancel_request_id',
        'transaction_sign',
        'item_id_mgm',
        'payment_id',
        'payment_tag',
        'item_split_tag',

        'data',
    ];
    protected $connection = 'mysql_getnet';
}
