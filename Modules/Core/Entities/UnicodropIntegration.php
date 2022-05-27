<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Activity;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ReportanaIntegrationPresenter;

/**
 * @property integer $id
 * @property int $user_id
 * @property int $project_id
 * @property string $token
 * @property boolean $billet_generated
 * @property boolean $billet_paid
 * @property boolean $credit_card_refused
 * @property boolean $credit_card_paid
 * @property boolean $abandoned_cart
 * @property boolean $pix
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Project $project
 * @property User $user
 */
class UnicodropIntegration extends Model
{
    use SoftDeletes, LogsActivity, PresentableTrait, HasFactory;
    /**
     * @var string
     */
    protected $presenter = ReportanaIntegrationPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'token',
        'billet_generated',
        'billet_paid',
        'credit_card_refused',
        'credit_card_paid',
        'abandoned_cart',
        'pix',
        'deleted_at',
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

    /**
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == 'deleted') {
            $activity->description = 'Integração Unicodrop para o projeto ' . $this->project->name . ' foi deletedo.';
        } else if ($eventName == 'updated') {
            $activity->description = 'Integração Unicodrop para o projeto ' . $this->project->name . ' foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Integração Unicodrop para o projeto ' . $this->project->name . ' foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
