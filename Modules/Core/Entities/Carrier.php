<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $site
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Delivery[] $deliveries
 * @property Project[] $projects
 */
class Carrier extends Model
{

    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 
        'site', 
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deliveries()
    {
        return $this->hasMany('Modules\Core\Entities\Delivery');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->hasMany('Modules\Core\Entities\Project');
    }
}
