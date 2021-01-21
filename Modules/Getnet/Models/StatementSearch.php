<?php

namespace Modules\Getnet\Models;

use Illuminate\Database\Eloquent\Model;

class StatementSearch extends Model
{
    public $fillable = [

        'subseller_id',
        'query_date',
        'date_init',
        'date_end',
        'ended_at',
        'list_transactions_count',
        'commission_count',
        'adjustments_count',
        'chargeback_count',
        'list_transactions_node',
        'commission_node',
        'adjustments_node',
        'chargeback_node',
        'data',
    ];
    protected $connection = 'mysql_getnet';
}
