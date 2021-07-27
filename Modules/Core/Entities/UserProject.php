<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\UserProjectsPresenter;

/**
 * @property integer $id
 * @property int $user_id
 * @property int $project_id
 * @property int $company_id
 * @property string $type
 * @property string $remuneration_value
 * @property boolean $access_permission
 * @property boolean $edit_permission
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Company $company
 * @property Project $project
 * @property User $user
 */
class UserProject extends Model
{
    use FoxModelTrait;
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;

    public const TYPE_PRODUCER_ENUM = 1;
    public const TYPE_PARTNER_ENUM = 2;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'users_projects';
    /**
     * @var string
     */
    protected $presenter = UserProjectsPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'company_id',
        'type_enum',
        'type',
        'remuneration_value',
        'access_permission',
        'edit_permission',
        'status_flag',
        'status',
        'order_priority',
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
     * Registra apenas os atributos alterados
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que o pacote armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;


    public function company(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\Company');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\Project');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\User');
    }
}
