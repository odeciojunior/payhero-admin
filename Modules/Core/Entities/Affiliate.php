<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property int $user_id
 * @property int $project_id
 * @property int $company_id
 * @property string $percentage
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
        'user_id', 
        'project_id', 
        'company_id', 
        'percentage', 
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('Modules\Core\Entities\Company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Modules\Core\Entities\Project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affiliateLinks()
    {
        return $this->hasMany('Modules\Core\Entities\AffiliateLink');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function campaigns()
    {
        return $this->hasMany('Modules\Core\Entities\Campaign');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientsCookies()
    {
        return $this->hasMany('Modules\Core\Entities\ClientsCookie');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->hasMany('Modules\Core\Entities\Sale');
    }
}
