<?php

namespace Modules\Getnet\Models;

use Illuminate\Database\Eloquent\Model;

class SaleSearch extends Model
{

    public $fillable = [
        'search_id',
        'sale_id',
        'list_transactions_count',
        'data',
    ];
    protected $connection = 'mysql_getnet';
}
