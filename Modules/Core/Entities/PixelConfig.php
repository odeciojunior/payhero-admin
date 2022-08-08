<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $project_id
 * @property string $metatags_facebook
 **/
class PixelConfig extends Model
{
    protected $fillable = ["project_id", "metatags_facebook"];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
