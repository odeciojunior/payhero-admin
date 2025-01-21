<?php

declare(strict_types=1);

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $gateway_id
 * @property string $name
 * @property string $slug
 * @property bool $card_flag_enum
 * @property bool $active_flag
 * @property string $created_at
 * @property string $updated_at
 * @property GatewayFlagTax[] $gatewayFlagTaxes
 * @property Gateway $gateway
 */
class GatewayFlag extends Model
{
    use LogsActivity;

    protected $keyType = "integer";

    protected $fillable = [
        "name",
        "slug",
        "gateway_id",
        "card_flag_enum",
        "active_flag",
        "created_at",
        "updated_at"
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    public function gatewayFlagTaxes(): HasMany
    {
        return $this->hasMany(GatewayFlagTax::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
    }
}
