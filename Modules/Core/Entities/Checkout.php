<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\CheckoutPresenter;
use App\Traits\LogsActivity;

/**
 * @property integer $id
 * @property int $project_id
 * @property int $affiliate_id
 * @property string $created_at
 * @property string $status
 * @property int $status_enum
 * @property string $operational_system
 * @property string $browser
 * @property string $id_log_session
 * @property string $ip
 * @property string $city
 * @property string $state
 * @property string $state_name
 * @property string $zip_code
 * @property string $country
 * @property string $parameter
 * @property string $currency
 * @property string $lat
 * @property string $lon
 * @property string $src
 * @property boolean $is_mobile
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $utm_source
 * @property string $utm_medium
 * @property string $utm_campaign
 * @property string $utm_term
 * @property string $utm_content
 * @property int $email_sent_amount
 * @property int $sms_sent_amount
 * @property Project $project
 * @property Affiliate $affiliate
 * @property CheckoutPlan[] $checkoutPlans
 * @property Log[] $logs
 * @property Sale[] $sales
 */
class Checkout extends Model
{
    use FoxModelTrait, SoftDeletes, PresentableTrait, LogsActivity;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var string
     */
    protected $presenter = CheckoutPresenter::class;
    /**
     * @var array
     */
    protected $fillable = [
        'project_id',
        'affiliate_id',
        'status',
        'status_enum',
        'operational_system',
        'browser',
        'id_log_session',
        'ip',
        'city',
        'state',
        'state_name',
        'zip_code',
        'country',
        'parameter',
        'currency',
        'lat',
        'lon',
        'src',
        'is_mobile',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'email_sent_amount',
        'sms_sent_amount',
        'client_name',
        'client_telephone',
        'template_type',
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
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Modules\Core\Entities\Project');
    }

    /**
     * @return BelongsTo
     */
    public function affiliate()
    {
        return $this->belongsTo('Modules\Core\Entities\Affiliate');
    }

    /**
     * @return HasMany
     */
    public function checkoutPlans()
    {
        return $this->hasMany('Modules\Core\Entities\CheckoutPlan');
    }

    /**
     * @return HasMany
     */
    public function logs()
    {
        return $this->hasMany('Modules\Core\Entities\Log');
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany('Modules\Core\Entities\Sale');
    }
}
