<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;


class SaleWoocommerceRequests extends Model
{
    
    /**
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'project_id',
        'method',
        'status',
        'send_data',
        'received_data',
        'created_at',
        'updated_at'
    ];
    
}
