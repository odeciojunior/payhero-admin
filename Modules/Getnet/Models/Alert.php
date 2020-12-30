<?php

namespace Modules\Getnet\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{

    public $fillable = [
        'search_id',
        'sale_id',
        'notes',
        'data',
    ];
    protected $connection = 'mysql_getnet';
}
