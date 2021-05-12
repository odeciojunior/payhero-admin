<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SaleWhiteBlackListResult
 * @package Modules\Core\Entities
 */
class SaleWhiteBlackListResult extends Model
{
    /**
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'whitelist',
        'blacklist',
        'whiteblacklist_json',
        'created_at',
        'updated_at',
    ];

    /**
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
