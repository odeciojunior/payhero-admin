<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\AffiliatePresenter;
use Spatie\Activitylog\LogOptions;

/**
 * @property integer $id
 * @property int $user_id
 * @property int $project_id
 * @property int $company_id
 * @property string $percentage
 * @property tinyinteger $status_enum
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Company $company
 * @property Project $project
 * @property User $user
 * @property AffiliateLink[] $affiliateLinks
 * @property Campaign[] $campaigns
 * @property ClientsCookie[] $clientsCookies
 * @property Sale[] $sales
 */
class Affiliate extends Model
{
    use SoftDeletes, LogsActivity, PresentableTrait, HasFactory;

    public const STATUS_ACTIVE = 1;
    public const STATUS_DISABLED = 2;

    /**
     * @var string
     */
    protected $presenter = AffiliatePresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "user_id",
        "project_id",
        "company_id",
        "percentage",
        "status_enum",
        "suport_phone_verified",
        "suport_phone",
        "suport_contact_verified",
        "suport_contact",
        "created_at",
        "updated_at",
        "deleted_at",
        "order_priority",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo("Modules\Core\Entities\Company");
    }

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo("Modules\Core\Entities\Project");
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo("Modules\Core\Entities\User");
    }

    /**
     * @return HasMany
     */
    public function affiliateLinks()
    {
        return $this->hasMany("Modules\Core\Entities\AffiliateLink");
    }

    /**
     * @return HasMany
     */
    public function campaigns()
    {
        return $this->hasMany("Modules\Core\Entities\Campaign");
    }

    /**
     * @return HasMany
     */
    public function clientsCookies()
    {
        return $this->hasMany("Modules\Core\Entities\ClientsCookie");
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany("Modules\Core\Entities\Sale");
    }
}
