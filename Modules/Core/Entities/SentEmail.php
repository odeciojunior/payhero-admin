<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class SentEmail
 * @package Modules\Core\Entities
 * @property string $from_email
 * @property string $from_name
 * @property string $to_email
 * @property string $to_name
 * @property string $template_id
 * @property json $template_data
 * @property integer $status_code
 * @property string $status
 * @property text $log_error
 */
class SentEmail extends Model
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
        'from_email',
        'from_name',
        'to_email',
        'to_name',
        'template_id',
        'template_data',
        'status_code',
        'status',
        'log_error',
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
}
