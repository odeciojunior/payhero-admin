<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * @property integer $id
 * @property int $sale_id
 * @property string $billet_link
 * @property string $billet_digitable_line
 * @property string $billet_due_date
 * @property string $gateway_transaction_id
 * @property int $gateway_billet_identificator
 * @property string $created_at
 * @property string $updated_at
 * @property Sale[] $sales
 */
class RegeneratedBillet extends Model
{
    use LogsActivity;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "sale_id",
        "billet_link",
        "billet_digitable_line",
        "billet_due_date",
        "gateway_transaction_id",
        "gateway_billet_identificator",
        "gateway_id",
        "owner_id",
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
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
