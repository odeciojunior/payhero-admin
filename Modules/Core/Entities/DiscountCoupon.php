<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\DiscountCouponsPresenter;

/**
 * @property integer $id
 * @property int $project_id
 * @property string $name
 * @property boolean $type
 * @property string $value
 * @property string $code
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 */
class DiscountCoupon extends Model
{

    use SoftDeletes;
    use PresentableTrait;

    protected $presenter = DiscountCouponsPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = [
        'project_id', 
        'name', 
        'type', 
        'value', 
        'code', 
        'status', 
        'rule_value',
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Modules\Core\Entities\Project');
    }
}
