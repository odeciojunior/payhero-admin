<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laracasts\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectUpsellConfig extends Model
{
    use PresentableTrait, FoxModelTrait, SoftDeletes;
    /**
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'project_id',
        'header',
        'title',
        'description',
        'countdown_time',
        'countdown_flag',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(ProjectUpsellRule::class);
    }
}

