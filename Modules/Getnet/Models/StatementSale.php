<?php

namespace Modules\Getnet\Models;

use Illuminate\Database\Eloquent\Model;

class StatementSale extends Model
{
    public $fillable = [

        'id',
        'hash_id',
    ];
    protected $connection = 'mysql_getnet';
}
