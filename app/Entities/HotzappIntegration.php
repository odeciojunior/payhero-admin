<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $company
 * @property string $description
 * @property string $link
 * @property string $created_at
 * @property string $updated_at
 * @property Company $company
 * @property Plan[] $plans
 */
class HotzappIntegration extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'hotzapp_integration';

    /**
     * @var array
     */
    protected $fillable = ['company', 'description', 'link', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Entities\Company', 'company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plans()
    {
        return $this->hasMany('App\Entities\Plan', 'hotzapp_integration');
    }
}
