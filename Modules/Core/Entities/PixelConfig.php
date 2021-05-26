<?php

namespace Modules\Core\Entities;

;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PixelConfig extends Model
{
    protected $fillable = [
        'project_id',
        'url_webhook_events_facebook',
        'metatags_facebook'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(PixelConfig::class);
    }
}
