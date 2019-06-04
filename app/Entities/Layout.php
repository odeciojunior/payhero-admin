<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $project
 * @property string $description
 * @property string $status
 * @property string $logo
 * @property string $format_logo
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 * @property Plan[] $plans
 */
class Layout extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['project', 'description', 'status', 'logo', 'format_logo', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Entities\Project', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plans()
    {
        return $this->hasMany('App\Entities\Plan', 'layout');
    }
}
