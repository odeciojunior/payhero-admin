<?php

namespace App\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $transaction
 * @property int $user
 * @property string $value
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 * @property Transaction $transaction
 * @property User $user
 */
class Transfer extends Model
{
    use FoxModelTrait;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'transaction',
        'user',
        'company_id',
        'value',
        'type',
        'type_enum',
        'reason',
        'created_at',
        'updated_at',
    ];
    /**
     * @var array
     */
    private $enum = [
        'type_enum' => [
            1 => 'in',
            2 => 'out',
        ],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo('App\Entities\Transaction', 'transaction');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Entities\User', 'user');
    }
}
