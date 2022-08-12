<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $user_id
 * @property int $company_id
 * @property integer $transaction_id
 * @property integer $sale_id
 * @property int $value
 * @property int $type_enum
 * @property int $status
 * @property float $percentage
 * @property string $created_at
 * @property string $updated_at
 * @property Company $company
 * @property Sale $sale
 * @property Transaction $transaction
 * @property User $user
 */
class Cashback extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = "integer";

    /**
     * @var array
     */
    protected $fillable = [
        "user_id",
        "company_id",
        "transaction_id",
        "sale_id",
        "value",
        "type_enum",
        "status",
        "percentage",
        "created_at",
        "updated_at",
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
