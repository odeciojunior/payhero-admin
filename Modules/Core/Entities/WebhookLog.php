<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class WebhookLog
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
 * @property Webhook $webhook
 * @property User $user
 * @property Company $company
 */
class WebhookLog extends Model
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
        "webhook_id",
        "user_id",
        "company_id",
        "sale_id",
        "url",
        "sent_data",
        "response",
        "response_status",
        "response_time",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    /**
     * @return BelongsTo
     */
    public function webhook()
    {
        return $this->belongsTo(Webhook::class);
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
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
