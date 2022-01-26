<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $company_id
 * @property integer $user_id
 * @property mixed $sent_data
 * @property mixed $response
 * @property string $created_at
 * @property string $updated_at
 */

class CheckoutApiPostback extends Model
{
    protected $keyType = 'integer';

    protected $fillable = [
        'company_id',
        'user_id',
        'sent_data',
        'response',
        'created_at',
        'updated_at'
    ];

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
