<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

/**
 * Class UserNotification
 * @package Modules\Core\Entities
 * @property int $user_id
 * @property bool $new_affiliation
 * @property bool $new_affiliation_request
 * @property bool $approved_affiliation
 * @property bool $boleto_compensated
 * @property bool $sale_approved
 * @property bool $retroactive_notazz
 * @property bool $withdrawal_approved
 * @property bool $domain_approved
 * @property bool $send_push_shopify_integration_ready
 * @property bool $user_shopify_integration_store
 * @property bool $ticket_open
 */
class UserNotification extends Model
{
    use LogsActivity;

    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'new_affiliation',
        'new_affiliation_request',
        'approved_affiliation',
        'boleto_compensated',
        'sale_approved',
        'notazz',
        'withdrawal_approved',
        'domain_approved',
        'shopify',
        'billet_generated',
        'ticket_open'
    ];
    /**
     * @var bool
     */
    protected static $logFillable = true;
    /**
     * @var bool
     */
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

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
