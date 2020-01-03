<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\WithdrawalPresenter;
use Spatie\Activitylog\Models\Activity;
use App\Traits\LogsActivity;

/**
 * @property integer $id
 * @property int $company_id
 * @property string $value
 * @property string $release_date
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $bank
 * @property string $agency
 * @property string $agency_digit
 * @property string $account
 * @property string $account_digit
 * @property Company $company
 */
class Withdrawal extends Model
{
    use PresentableTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = WithdrawalPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'company_id',
        'value',
        'release_date',
        'status',
        'created_at',
        'updated_at',
        'bank',
        'agency',
        'agency_digit',
        'account',
        'account_digit',
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
     * Registra apenas os atributos alterados
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que o pacote armazene logs vazios
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
            $activity->description = 'Pedido transferência foi deletedo.';
        } else if ($eventName == 'updated') {
            $activity->description = 'Pedido transferência foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Pedido transferência foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('Modules\Core\Entities\Company');
    }
}
