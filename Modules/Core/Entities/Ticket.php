<?php

namespace Modules\Core\Entities;

use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\TicketPresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property integer $sale_id
 * @property integer $customer_id
 * @property string $subject
 * @property integer $subject_enum
 * @property string $description
 * @property integer $ticket_subcategory_enum
 * @property integer $ticket_status_enum
 * @property integer $last_message_type_enum
 * @property string $last_message_date
 * @property boolean $mediation_notified
 * @property boolean $ignore_balance_block
 * @property integer $classification_enum
 * @property integer $average_response_time
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Sale $sale
 * @property Customer $customer
 * @property Collection $attachments
 * @property Collection $messages
 * @method TicketPresenter present()
 */
class Ticket extends Model
{
    use PresentableTrait, LogsActivity, HasFactory;

    const CATEGORY_COMPLAINT = 1;
    const CATEGORY_DOUBT = 2;
    const CATEGORY_SUGGESTION = 3;

    //Delivered item
    const SUBJECT_DIFFERS_FROM_ADVERTISED = 1;
    const SUBJECT_DAMAGED_BY_TRANSPORT = 2;
    const SUBJECT_MANUFACTURING_DEFECT = 3;

    //Not delivered item
    const SUBJECT_TRACKING_CODE_NOT_RECEIVED = 4;
    const SUBJECT_NON_TRACKABLE_ORDER = 5;
    const SUBJECT_DELIVERY_DELAY = 6;
    const SUBJECT_DELIVERY_TO_WRONG_ADDRESS = 7;

    //Others
    const SUBJECT_OTHERS = 8;

    const STATUS_OPEN = 1;
    const STATUS_CLOSED = 2;
    const STATUS_MEDIATION = 3;

    /**
     * @var string
     */
    protected $presenter = TicketPresenter::class;
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
        "customer_id",
        "subject",
        "subject_enum",
        "description",
        "ticket_category_enum",
        "ticket_status_enum",
        "last_message_type_enum",
        "last_message_date",
        "mediation_notified",
        "ignore_balance_block",
        "classification_enum",
        "average_response_time",
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
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * @return BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return HasMany
     */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * @return HasMany
     */
    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }
}
