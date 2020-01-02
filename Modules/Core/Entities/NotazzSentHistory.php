<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\NotazzSentHistoryPresenter;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property integer $id
 * @property integer $notazz_invoice_id
 * @property boolean $sent_type_enum
 * @property string $url
 * @property string $data_sent
 * @property string $response
 * @property string $created_at
 * @property string $updated_at
 * @property NotazzInvoice $notazzInvoice
 */
class NotazzSentHistory extends Model
{
    use PresentableTrait, FoxModelTrait, LogsActivity;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var string
     */
    protected $presenter = NotazzSentHistoryPresenter::class;
    /**
     * @var array
     */
    protected $fillable = [
        'notazz_invoice_id',
        'sent_type_enum',
        'url',
        'data_sent',
        'response',
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
    public function notazzInvoice()
    {
        return $this->belongsTo('Modules\Core\Entities\NotazzInvoice');
    }
}
