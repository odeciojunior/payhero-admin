<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AntifraudPostback extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $keyType = 'integer';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'sale_id',
        'antifraud_id',
        'data',
        'processed_flag',
        'postback_valid_flag',
        'machine_result',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function antifraud(): BelongsTo
    {
        return $this->belongsTo(Antifraud::class);
    }
}
