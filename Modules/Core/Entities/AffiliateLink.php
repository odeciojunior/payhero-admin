<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

/**
 * @property integer $id
 * @property integer $affiliate_id
 * @property integer $campaign_id
 * @property integer $plan_id
 * @property string $parameter
 * @property string $link
 * @property integer $clicks_amount
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Affiliate $affiliate
 * @property Campaign $campaign
 * @property Plan $plan
 */
class AffiliateLink extends Model
{
    use SoftDeletes, LogsActivity;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'affiliate_id',
        'campaign_id',
        'plan_id',
        'parameter',
        'link',
        'clicks_amount',
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
    public function affiliate()
    {
        return $this->belongsTo('Modules\Core\Entities\Affiliate');
    }

    /**
     * @return BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo('Modules\Core\Entities\Campaign');
    }

    /**
     * @return BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo('Modules\Core\Entities\Plan');
    }
}
