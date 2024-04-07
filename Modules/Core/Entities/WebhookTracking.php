<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class WebhookTracking
 * @package Modules\Core\Entities
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $project_id
 * @property integer $token_id
 * @property string $clientid
 * @property string $webhook_url
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 * @property Project $project
 * @property Token $token
 */
class WebhookTracking extends Model
{
    use SoftDeletes;

    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";

    /**
     * @var array
     */
    protected $fillable = [
        "user_id",
        "project_id",
        "token_id",
        "clientid",
        "webhook_url",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
   
    /**
     * @return BelongsTo
     */
    public function token()
    {
        return $this->belongsTo(ApiToken::class);
    }
}
