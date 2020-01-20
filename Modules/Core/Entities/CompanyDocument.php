<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\CompanyDocumentPresenter;
use App\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property int $company_id
 * @property string $document_url
 * @property boolean $document_type_enum
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property Company $company
 */
class CompanyDocument extends Model
{
    use PresentableTrait, FoxModelTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = CompanyDocumentPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'company_id',
        'document_url',
        'document_type_enum',
        'status',
        'refused_reason',
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
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == 'deleted') {
            $activity->description = 'Documento da empresa foi deletado.';
        } else if ($eventName == 'updated') {
            $activity->description = 'Documento da empresa foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Documento da empresa foi criado';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('Modules\Core\Entities\Company');
    }
}
