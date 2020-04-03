<?php

namespace Modules\Core\Entities;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserTerms
 * @package Modules\Core\Entities
 */
class UserTerms extends Model
{
    use SoftDeletes, LogsActivity;
    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'update_at',
        'deleted_at',
    ];
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'term_version',
        'device_data',
        'accepted_at',
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
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User');
    }
}
