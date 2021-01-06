<?php

namespace Modules\Getnet\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{

    public $fillable = [
        'id',
        'hash_id',
        'order_id',
    ];
    protected $connection = 'mysql_getnet';
}
