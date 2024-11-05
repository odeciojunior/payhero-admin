<?php

declare(strict_types=1);

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

class ShortIo extends Model
{
    protected $table = 'short_links';

    protected $fillable = [
        'short_id',
        'original_url',
        'expires_at',
    ];
}
