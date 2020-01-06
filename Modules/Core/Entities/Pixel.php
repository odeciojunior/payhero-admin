<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\PixelPresenter;
use App\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $project_id
 * @property integer $campaign_id
 * @property string $name
 * @property string $code
 * @property string $platform
 * @property boolean $status
 * @property string $checkout
 * @property string $purchase_boleto
 * @property string $purchase_card
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Campaign $campaign
 * @property Project $project
 */
class Pixel extends Model
{
    use SoftDeletes, FoxModelTrait, PresentableTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = PixelPresenter::class;
    /**
     * @var array
     */
    protected $fillable = [
        'project_id',
        'campaign_id',
        'name',
        'code',
        'platform',
        'status',
        'checkout',
        'purchase_boleto',
        'purchase_card',
        'created_at',
        'updated_at',
        'deleted_at',
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
    public function campaign()
    {
        return $this->belongsTo('Modules\Core\Entities\Campaign');
    }

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Modules\Core\Entities\Project');
    }
}
