<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

/**
 * @property integer $id
 * @property int $project_id
 * @property int $user_id
 * @property string $token_webhook
 * @property string $token_api
 * @property string $token_logistics
 * @property string $created_at
 * @property string $updated_at
 * @property Project $project
 * @property User $user
 */
class NotazzIntegration extends Model
{
    use SoftDeletes, LogsActivity, HasFactory;
    /**
     * @var array
     */
    protected $dates = ["deleted_at"];
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "project_id",
        "user_id",
        "token_webhook",
        "token_api",
        "token_logistics",
        "start_date", //data inicial da geracao das notas
        "retroactive_generated_date", //data da geração das notas retroativas
        "invoice_type",
        "pending_days",
        "generate_zero_invoice_flag",
        "discount_plataform_tax_flag",
        "active_flag",
        "created_at",
        "updated_at",
        "deleted_at",
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
    public function project()
    {
        return $this->belongsTo("Modules\Core\Entities\Project");
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo("Modules\Core\Entities\User");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany("Modules\Core\Entities\NotazzInvoice");
    }
}
