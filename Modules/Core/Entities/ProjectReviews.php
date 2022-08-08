<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

class ProjectReviews extends Model
{
    use PresentableTrait, FoxModelTrait, SoftDeletes;

    /**
     * @var string
     */
    protected $keyType = "integer";

    /**
     * @var array
     */
    protected $fillable = [
        "project_id",
        "description",
        "apply_on_plans",
        "photo",
        "name",
        "stars",
        "active_flag",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
