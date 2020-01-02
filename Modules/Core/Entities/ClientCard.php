<?php

namespace ModulesCoreEntities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property integer $id
 * @property integer $client_id
 * @property int $first_four_digits
 * @property int $last_four_digits
 * @property string $card_token
 * @property string $association_code
 * @property string $created_at
 * @property string $updated_at
 * @property Client $client
 */
class ClientCard extends Model
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
        'browser_fingerprint',
        'client_id',
        'first_four_digits',
        'last_four_digits',
        'card_token',
        'association_code',
        'created_at',
        'updated_at',
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
     * Registra apenas os atributos alterados no log
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;

    /**
     * @return BelongsTo
     */
    public function client()
    {
        return $this->belongsTo('ModulesCoreEntities\Client');
    }
}
