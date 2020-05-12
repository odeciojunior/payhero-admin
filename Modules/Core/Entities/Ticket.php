<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\TicketPresenter;
use App\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property integer $sale_id
 * @property integer $customer_id
 * @property string $subject
 * @property string $description
 * @property integer $ticket_category_enum
 * @property integer $ticket_status_enum
 * @property boolean $mediation_notified
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
    use PresentableTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = TicketPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'customer_id',
        'subject',
        'description',
        'ticket_category_enum',
        'ticket_status_enum',
        'mediation_notified',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * @var bool
     */
    protected static $logFillable = true;
    /**
     * @var bool
     */
    protected static $logUnguarded = true;
    /**
     * Registra apenas os atributos alterados no log
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;

    /**
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == 'deleted') {
            $activity->description = 'O chamado foi deletedo.';
        } else if ($eventName == 'updated') {
            $activity->description = 'O chamado foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Chamado criado.';
        } else {
            $activity->description = $eventName;
        }
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

    /**
     * @return HasMany
     */
    public function lastMessage()
    {
        return $this->hasMany(TicketMessage::class)
            ->latest();
    }
}
