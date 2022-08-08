<?php

namespace Modules\Core\Entities;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Activity;

/**
 * Class Whatsapp2Integration
 * @package Modules\Core\Entities
 * @property integer $id
 * @property int $user_id
 * @property int $project_id
 * @property string $api_token
 * @property string $url_order
 * @property string $url_checkout
 * @property boolean $billet_generated
 * @property boolean $billet_paid
 * @property boolean $credit_card_refused
 * @property boolean $credit_card_paid
 * @property boolean $abandoned_cart
 * @property boolean $pix_expired
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Project $project
 * @property User $user
 */
class Whatsapp2Integration extends Model
{
    use SoftDeletes, LogsActivity;

    public const STATUS_PENDING = "pending";
    public const STATUS_PAID = "paid";
    public const STATUS_CANCELLED = "order_cancelled";
    public const STATUS_VOIDED = "voided";

    protected $keyType = "integer";

    protected $fillable = [
        "user_id",
        "project_id",
        "api_token",
        "url_order",
        "url_checkout",
        "billet_generated",
        "billet_paid",
        "credit_card_refused",
        "credit_card_paid",
        "abandoned_cart",
        "pix_expired",
        "pix_paid",
        "deleted_at",
        "created_at",
        "updated_at",
    ];

    protected static $logFillable = true;

    protected static $logUnguarded = true;
    /**
     * Registra apenas os atributos alterados
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que o pacote armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;

    public function tapActivity(Activity $activity, string $eventName)
    {
        switch ($eventName) {
            case "deleted":
                $activity->description = "Integração whatsapp 2.0 para o projeto {$this->project->name} foi deletedo.";
                break;
            case "created":
                $activity->description = "Integração whatsapp 2.0 para o projeto {$this->project->name} foi criado";
                break;
            case "updated":
                $activity->description = "Integração whatsapp 2.0 para o projeto {$this->project->name} foi atualizada.";
                break;
            default:
                $activity->description = $eventName;
        }
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
