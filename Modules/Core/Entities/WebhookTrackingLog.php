<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class WebhookTrackingLog
 * @package Modules\Core\Entities
 *
 * @property integer $id
 * @property integer $webhook_id
 * @property integer $user_id
 * @property integer $company_id
 * @property integer $sale_id
 * @property string $url
 * @property json $sent_data
 * @property json $response
 * @property integer $response_status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property WebhookTracking $webhooktracking
  * @property User $user
 * @property Project $project
 * @property Sale $sale
 */
class WebhookTrackingLog extends Model
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
        "webhook_tracking_id",
        "user_id",
        "project_id",
        "sale_id",
        "url",
        "sent_data",
        "response",
        "response_status",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    /**
     * @return BelongsTo
     */
    public function webhookTracking()
    {
        return $this->belongsTo(WebhookTracking::class);
    }

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
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
