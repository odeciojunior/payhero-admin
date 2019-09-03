<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'project', 
        'description', 
        'title', 
        'photo', 
        'link', 
        'type', 
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Modules\Core\Entities\Project', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planGifts()
    {
        return $this->hasMany('Modules\Core\Entities\PlanGift');
    }
}
