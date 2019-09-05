<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ShippingPresenter;

/**
 * @property integer $id
 * @property int $project_id
 * @property string $name
 * @property string $information
 * @property string $value
 * @property string $type
 * @property string $zip_code_origin
 * @property boolean $status
 * @property boolean $pre_selected
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 * @property Sale[] $sales
 */
class Shipping extends Model
{
    use SoftDeletes, FoxModelTrait, PresentableTrait;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $appends = ['id_code'];
    /**
     * @var array
     */

    protected $presenter = ShippingPresenter::class;
    protected $fillable = [
        'project_id',
        'name',
        'information',
        'value',
        'type',
        'zip_code_origin',
        'status',
        'pre_selected',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Modules\Core\Entities\Project');
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany('Modules\Core\Entities\Sale');
    }
}
