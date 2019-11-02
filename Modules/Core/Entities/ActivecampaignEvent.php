<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $activecampaign_integration_id
 * @property integer $product_id
 * @property integer $plan_id
 * @property int $event_sale
 * @property string $add_tags
 * @property string $remove_tags
 * @property int $remove_list
 * @property int $add_list
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property ActivecampaignIntegration $activecampaignIntegration
 * @property Plan $plan
 * @property Product $product
 */
class ActivecampaignEvent extends Model
{
    use SoftDeletes;
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = [
        'activecampaign_integration_id',
        'product_id',
        'plan_id',
        'event_sale',
        'add_tags',
        'remove_tags',
        'remove_list',
        'add_list',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activecampaignIntegration()
    {
        return $this->belongsTo('Modules\Core\Entities\ActivecampaignIntegration');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo('Modules\Core\Entities\Plan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('Modules\Core\Entities\Product');
    }
}
