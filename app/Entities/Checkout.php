<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $project
 * @property string $id_log_session
 * @property string $status
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
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $utm_source
 * @property string $utm_medium
 * @property string $utm_campaign
 * @property string $utm_term
 * @property string $utm_content
 * @property string $src
 * @property Project $project
 * @property CheckoutPlan[] $checkoutPlans
 * @property Sale[] $sales
 */
class Checkout extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'project',
        'id_log_session',
        'status',
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
        'created_at',
        'updated_at',
        'deleted_at',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'src',
        'is_mobile',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projectModel()
    {
        return $this->belongsTo('App\Entities\Project', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checkoutPlans()
    {
        return $this->hasMany('App\Entities\CheckoutPlan', 'checkout');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->hasMany('App\Entities\Sale', 'checkout');
    }
}
