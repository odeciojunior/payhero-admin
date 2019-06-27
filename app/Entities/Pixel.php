<?php

namespace App\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $project
 * @property integer $campaign
 * @property string $name
 * @property string $code
 * @property string $platform
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $checkout
 * @property string $purchase_boleto
 * @property string $purchase_card
 * @property Campaign $campaign
 * @property Project $project
 */
class Pixel extends Model
{
    use SoftDeletes;
    use FoxModelTrait;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * @var array
     */
    protected $fillable = [
        'project',
        'campaign',
        'name',
        'code',
        'platform',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'checkout',
        'purchase_boleto',
        'purchase_card',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo('App\Entities\Campaign', 'campaign');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Entities\Project', 'project');
    }
}
