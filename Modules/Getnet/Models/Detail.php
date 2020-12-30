<?php

namespace Modules\Getnet\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{

    public $fillable = [
        'search_id',
        'sale_id',
        'summary_id',
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
        'cpfcnpj_subseller',
        'cancel_custom_key',
        'cancel_request_id',
        'marketplace_transaction_id',
        'cnpj_marketplace',
        'transaction_date',
        'confirmation_date',
        'item_id',
        'number_installments',
        'installment',
        'installment_date',
        'installment_amount',
        'subseller_rate_amount',
        'subseller_rate_percentage',
        'payment_date',
        'subseller_rate_closing_date',
        'subseller_rate_confirm_date',
        'subseller_id',
        'seller_id',
        'transaction_sign',
        'item_id_mgm',
        'payment_id',
        'payment_tag',
        'item_split_tag',
    ];
    protected $connection = 'mysql_getnet';
}
