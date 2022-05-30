<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ShopifyIntegrationPresenter;
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
 * @property bool $status
 * @property bool $skip_to_cart
 * @property Project $project
 * @property User $user
 */
class ShopifyIntegration extends Model
{
    use FoxModelTrait;
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;
    use HasFactory;

    public const SHOPIFY_BASIC_THEME = 1;
    public const SHOPIFY_AJAX_THEME = 2;

    public const STATUS_PENDING = 1;
    public const STATUS_APPROVED = 2;
    public const STATUS_DISABLED = 3;

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
        switch ($eventName) {
            case 'deleted':
                $activity->description = 'Integração com shopify foi deleteda.';
                break;
            case 'updated':
                $activity->description = 'Integração com shopify foi atualizado.';
                break;
            case 'created':
                $activity->description = 'Integração com shopify foi criado.';
                break;
            default:
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
