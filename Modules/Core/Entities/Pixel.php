<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\PixelPresenter;
use Spatie\Activitylog\Models\Activity;

/**
 * @property int $id
 * @property int $project_id
 * @property string $name
 * @property string $code
 * @property string $platform
 * @property bool $status
 * @property string $checkout
 * @property string $purchase_boleto
 * @property string $purchase_card
 * @property int $affiliate_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 */
class Pixel extends Model
{
    use FoxModelTrait;
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;

    public const STATUS_ACTIVE = 1;
    public const STATUS_DISABLED = 0;

    public const FACEBOOK_PLATFORM = 'facebook';
    public const GOOGLE_ADWORDS_PLATFORM = 'google_adwords';
    public const GOOGLE_ANALYTICS_PLATFORM = 'google_analytics';
    public const GOOGLE_ANALYTICS_FOUR_PLATFORM = 'google_analytics_four';
    public const TABOOLA_PLATFORM = 'taboola';
    public const OUTBRAIN_PLATFORM = 'outbrain';
    public const PINTEREST_PLATFORM = 'pinterest';

    /**
     * @var string
     */
    protected $presenter = PixelPresenter::class;
    /**
     * @var array
     */
    protected $fillable = [
        'project_id',
        'campaign_id',
        'name',
        'code',
        'platform',
        'status',
        'checkout',
        'purchase_boleto',
        'purchase_card',
        'purchase_pix',
        'affiliate_id',
        'apply_on_plans',
        'code_meta_tag_facebook',
        'purchase_event_name',
        'is_api',
        'facebook_token',
        'value_percentage_purchase_boleto',
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
                $activity->description = 'Pixel ' . $this->name . ' foi deletedo.';
                break;
            case 'updated':
                $activity->description = 'Pixel ' . $this->name . ' foi atualizado.';
                break;
            case 'created':
                $activity->description = 'Pixel ' . $this->name . ' foi criado.';
                break;
            default:
                $activity->description = $eventName;
        }
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }
}
