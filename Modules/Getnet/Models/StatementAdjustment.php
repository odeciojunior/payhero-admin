<?php

namespace Modules\Getnet\Models;

use Illuminate\Database\Eloquent\Model;

class StatementAdjustment extends Model
{
    public $fillable = [
        'statement_search_id',

        'type_register',
        'bank',
        'agency',
        'account_number',
        'account_type',
        'marketplace_subsellerid',
        'adjustment_origin',
        'marketplace_schedule_id',
        'nu_liquid',
        'merchand_id',
        'cpfcnpj_subseller',
        'cnpj_marketplace',
        'adjustment_id',
        'adjustment_type',
        'adjustment_date',
        'adjustment_amount',
        'subseller_rate_closing_date',
        'subseller_rate_confirm_date',
        'payment_date',
        'transaction_sign',
        'adjustment_reason',
        'order_id',
        'product_id',
        'our_number',
        'nsu_boleto_adjustment',

        'data',
    ];
    protected $connection = 'mysql_getnet';
}
