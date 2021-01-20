<?php

namespace Modules\Getnet\Models;

use Illuminate\Database\Eloquent\Model;

class StatementChargeback extends Model
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
        'release_status',
        'merchand_id',
        'marketplace_transaction_id',
        'transaction_date',
        'installment_date',
        'subseller_rate_confirm_date',
        'chargeback_amount',
        'seller_id',
        'transaction_sign',
        'adjustment_id',
        'adjustment_type',
        'adjustment_date',
        'adjustment_amount',
        'adjustment_origin',
        'adjustment_reason',
        'acquirer_transaction_id',
        'card_payment_amount',
        'marketplace_original_transaction_id',
        'transaction_status_code',
        'terminal_nsu',
        'reason_message',
        'authorization_code',
        'order_id',
        'vlr_mdr_marketplace',
        'vlr_fee',
        'vlr_somaMdrFee',
        'perc_mdr_marketplace',
        'perc_fee',
        'perc_somaMdrFee',
        'product_id',

        'data',
    ];
    protected $connection = 'mysql_getnet';
}
