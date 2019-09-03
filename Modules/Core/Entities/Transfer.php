<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $transaction_id
 * @property int $user_id
 * @property int $company_id
 * @property string $value
 * @property string $type
 * @property int $type_enum
 * @property string $reason
 * @property string $created_at
 * @property string $updated_at
 * @property Transaction $transaction
 * @property User $user
 * @property Company $company
 */
class Transfer extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = [
        'transaction_id', 
        'user_id', 
        'company_id', 
        'value', 
        'type', 
        'type_enum', 
        'reason', 
        'created_at', 
        'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo('App\Entities\Transaction');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Entities\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Entities\Company');
    }
}
