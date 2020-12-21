<?php

namespace Modules\Core\Entities;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\WithdrawalPresenter;
use Spatie\Activitylog\Models\Activity;

/**
 * @property int $id
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
 * @property string $file
 * @property string $automatic_liquidation
 * @property Company $company
 */
class Withdrawal extends Model
{
    use LogsActivity;
    use PresentableTrait;

    protected $presenter = WithdrawalPresenter::class;

    protected $keyType = 'integer';

    protected $dates = [
        'release_date',
        'created_at',
        'updated_at',
        'release_date_new'
    ];

    protected $fillable = [
        'company_id',
        'value',
        'release_date',
        'status',
        'currency',
        'currency_quotation',
        'value_transferred',
        'tax',
        'bank',
        'agency',
        'agency_digit',
        'account',
        'account_digit',
        'release_date_new',
        'file',
        'observation',
        'automatic_liquidation',
        'is_released',
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
     * Registra apenas os atributos alterados
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que o pacote armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == 'deleted') {
            $activity->description = 'Pedido saque foi deletedo.';
        } elseif ($eventName == 'updated') {
            $activity->description = 'Pedido saque foi atualizado.';
        } elseif ($eventName == 'created') {
            $activity->description = 'Pedido saque foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\Company');
    }
}
