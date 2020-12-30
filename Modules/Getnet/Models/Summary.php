<?php

namespace Modules\Getnet\Models;

use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{

    public $fillable = [
        'search_id',
        'sale_id',
        'details_count',
        'type_register',
        'order_id',
        'seller_id',
        'marketplace_subsellerid',
        'merchand_id',
        'cnpj_marketplace',
        'marketplace_transaction_id',
        'transaction_date',
        'confirmation_date',
        'product_id',
        'transaction_type',
        'number_installments',
        'nsu_host',
        'acquirer_transaction_id',
        'card_payment_amount',
        'sum_details_card_payment_amount',
        'marketplace_original_transaction_id',
        'transaction_status_code',
        'transaction_sign',
        'terminal_nsu',
        'reason_message',
        'authorization_code',
        'payment_id',
        'terminal_identification',
        'nsu_tef',
        'entry_mode',
        'transaction_channel',
        'capture',
        'payment_tag',
    ];
    protected $connection = 'mysql_getnet';
}
