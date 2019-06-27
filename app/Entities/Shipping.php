<?php

namespace App\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property int $project
 * @property string $name
 * @property string $information
 * @property string $value
 * @property string $type
 * @property string $zip_code_origin
 * @property boolean $status
 * @property boolean $pre_selected
 * @property string $created_at
 * @property string $updated_at
 * @property Project $project
 */
class Shipping extends Model
{
    use FoxModelTrait;
    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'project',
        'name',
        'information',
        'value',
        'type',
        'zip_code_origin',
        'status',
        'pre_selected',
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Project', 'project');
    }
}
