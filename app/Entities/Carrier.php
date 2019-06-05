<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $site
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Delivery[] $deliveries
 * @property Plan[] $plans
 * @property Project[] $projects
 */
class Carrier extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'site', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deliveries()
    {
        return $this->hasMany('App\Entities\Delivery', 'carrier');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plans()
    {
        return $this->hasMany('App\Entities\Plan', 'carrier');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->hasMany('App\Entities\Project', 'carrier');
    }
}
