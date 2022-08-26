<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Entities\Customer;
use Spatie\Activitylog\LogOptions;

/**
 * @property integer $id
 * @property integer $client_id
 * @property int $first_four_digits
 * @property int $last_four_digits
 * @property string $card_token
 * @property string $association_code
 * @property boolean $deleted_by_user
 * @property string $created_at
 * @property string $updated_at
 * @property Customer $customer
 */
class CustomerCard extends Model
{
    use LogsActivity, HasFactory;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "browser_fingerprint",
        "customer_id",
        "first_four_digits",
        "last_four_digits",
        "card_token",
        "association_code",
        "deleted_by_user",
        "created_at",
        "updated_at",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    /**
     * @return BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo("Modules\Core\Entities\Customer");
    }
}
