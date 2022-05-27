<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\WooCommerceIntegrationPresenter;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Activity;

/**
 * @property int $id
 * @property int $user_id
 * @property int $project_id
 * @property string $token_user
 * @property string $token_pass
 * @property string $url_store
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 * @property User $user
 */
class WooCommerceIntegration extends Model
{
    use SoftDeletes, FoxModelTrait, PresentableTrait, LogsActivity, HasFactory;
    /**
     * @var string
     */
    protected $presenter = WooCommerceIntegrationPresenter::class;
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'token_user',
        'token_pass',
        'url_store',
        'status',
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
            $activity->description = 'Integração com woocommerce foi deleteda.';
        } else if ($eventName == 'updated') {
            $activity->description = 'Integração com woocommerce foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Integração com woocommerce foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
