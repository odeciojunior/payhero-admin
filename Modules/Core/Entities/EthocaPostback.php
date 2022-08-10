<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class EthocaPostback extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'data',
        'is_cloudfox',
        'processed_flag',
        'machine_result',
        'created_at',
        'updated_at'
    ];

    /**
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

}
