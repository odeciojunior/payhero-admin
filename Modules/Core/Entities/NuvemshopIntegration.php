<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NuvemshopIntegration extends Model
{
    use SoftDeletes;

    protected $table = "nuvemshop_integrations";

    public const STATUS_PENDING = "PENDING";
    public const STATUS_ACTIVE = "ACTIVE";
    public const STATUS_INACTIVE = "INACTIVE";

    protected $fillable = [
        "user_id",
        "project_id",
        "url_store",
        "token",
        "store_id",
        "status",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
