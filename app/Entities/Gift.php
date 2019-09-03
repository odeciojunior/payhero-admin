<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $project
 * @property string $description
 * @property string $title
 * @property string $photo
 * @property string $link
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 * @property PlanGift[] $planGifts
 */
class Gift extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['project', 'description', 'title', 'photo', 'link', 'type', 'created_at', 'updated_at', 'deleted_at'];

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
    public function planGifts()
    {
        return $this->hasMany('App\Entities\PlanGift');
    }
}
