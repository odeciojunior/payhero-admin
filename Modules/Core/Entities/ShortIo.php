<?php

declare(strict_types=1);

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShortIo extends Model
{
    use SoftDeletes;
    
    protected $table = 'short_links';

    protected $fillable = [
        'short_id',
        'original_url',
        'expires_at',
    ];
}
