<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\TransferPresenter;
use App\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

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
    use FoxModelTrait, PresentableTrait, LogsActivity;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var string
     */
    protected $presenter = TransferPresenter::class;
    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];
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
        'updated_at',
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
            $activity->description = 'Extrato foi deletedo.';
        } else if ($eventName == 'updated') {
            $activity->description = 'Extrato foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Extrato foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo('Modules\Core\Entities\Transaction');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User');
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('Modules\Core\Entities\Company');
    }
}
