<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserNotification
 * @package Modules\Core\Entities
 * @property integer $user_id
 * @property boolean $new_affiliation
 * @property boolean $new_affiliation_request
 * @property boolean $approved_affiliation
 * @property boolean $boleto_compensated
 * @property boolean $sale_approved
 * @property boolean $retroactive_notazz
 * @property boolean $withdrawal_approved
 * @property boolean $released_balance
 * @property boolean $domain_approved
 * @property boolean $send_push_shopify_integration_ready
 * @property boolean $user_shopify_integration_store
 */
class UserNotification extends Model
{
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
        'released_balance',
        'domain_approved',
        'shopify',
        'billet_generated',
        'credit_card_in_proccess',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
