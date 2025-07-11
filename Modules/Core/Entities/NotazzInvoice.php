<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\NotazzInvoicePresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property integer $id
 * @property integer $sale_id
 * @property boolean $invoice_type
 * @property string $notazz_id
 * @property string $external_id
 * @property boolean $status
 * @property string $schedule
 * @property int $attempts
 * @property string $created_at
 * @property string $updated_at
 * @property Sale $sale
 */

/**
 * Class NotazzInvoice
 * @package App\Entities
 */
class NotazzInvoice extends Model
{
    use FoxModelTrait, SoftDeletes, PresentableTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = NotazzInvoicePresenter::class;
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
        "sale_id",
        "notazz_integration_id",
        "currency_quotation_id",
        "invoice_type",
        "notazz_id",
        "external_id",
        "status",
        "canceled_flag",
        "schedule",
        "attempts",
        "xml",
        "pdf",
        "logistic_id",
        "notazz_status",
        "max_attempts",
        "date_last_attempt",
        "date_pending",
        "date_sent",
        "date_completed",
        "date_error",
        "date_rejected",
        "date_canceled",
        "return_message",
        "return_http_code",
        "return_status",
        "data_json",
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
    public function sale()
    {
        return $this->belongsTo("Modules\Core\Entities\Sale");
    }

    /**
     * @return BelongsTo
     */
    public function notazzIntegration()
    {
        return $this->belongsTo("Modules\Core\Entities\NotazzIntegration");
    }
}
