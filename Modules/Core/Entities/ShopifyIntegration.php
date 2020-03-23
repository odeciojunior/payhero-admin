<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ShopifyIntegrationPresenter;
use App\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * @property int $id
 * @property int $user_id
 * @property int $project_id
 * @property string $token
 * @property string $shared_secret
 * @property string $url_store
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property int $theme_type
 * @property string $theme_name
 * @property string $theme_file
 * @property string $theme_html
 * @property string $layout_theme_html
 * @property boolean $status
 * @property boolean $skip_to_cart
 * @property Project $project
 * @property User $user
 */
class ShopifyIntegration extends Model
{
    use SoftDeletes, FoxModelTrait, PresentableTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = ShopifyIntegrationPresenter::class;
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'token',
        'shared_secret',
        'url_store',
        'theme_type',
        'theme_name',
        'theme_file',
        'theme_html',
        'layout_theme_html',
        'status',
        'skip_to_cart',
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
            $activity->description = 'Integração com shopify foi deleteda.';
        } else if ($eventName == 'updated') {
            $activity->description = 'Integração com shopify foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Integração com shopify foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Modules\Core\Entities\Project');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User');
    }
}
