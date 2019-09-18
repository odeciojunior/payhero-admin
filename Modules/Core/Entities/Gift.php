<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $project_id
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
    use FoxModelTrait, SoftDeletes;
    /**
     * @var array
     */
    protected $appends = ['id_code'];
    /**
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    /**
     * @var array
     */
    protected $fillable = [
        'project_id',
        'description',
        'title',
        'photo',
        'link',
        'type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Modules\Core\Entities\Project');
    }

    /**
     * @return HasMany
     */
    public function planGifts()
    {
        return $this->hasMany('Modules\Core\Entities\PlanGift');
    }
}
