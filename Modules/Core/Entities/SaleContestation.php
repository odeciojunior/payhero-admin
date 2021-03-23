<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SaleContestation
 * @package Modules\Core\Entities
 * @property integer $id
 * @property integer $sale_id
 * @property json $data
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Sale $sale
 */

class SaleContestation extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'data',
        'nsu',
        'file_date',
        'transaction_date',
        'request_date',
        'reason',
        'observation',
        'is_contested',
        'file_user_completed',
        'expiration_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function files()
    {
        return $this->hasMany(SaleContestationFile::class, 'contestation_sale_id');
    }
}
