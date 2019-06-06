<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $affiliate
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Affiliate $affiliate
 * @property AffiliateLink[] $affiliateLinks
 * @property Pixel[] $pixels
 */
class Campaign extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['affiliate', 'description', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function affiliate()
    {
        return $this->belongsTo('App\Entities\Affiliate', 'affiliate');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affiliateLinks()
    {
        return $this->hasMany('App\Entities\AffiliateLink', 'campaign');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pixels()
    {
        return $this->hasMany('App\Entities\Pixel', 'campaign');
    }
}
