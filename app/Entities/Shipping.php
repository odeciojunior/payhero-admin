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
    use FoxModelTrait, SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'information',
        'name',
        'pre_selected',
        'project',
        'status',
        'type',
        'value',
        'zip_code_origin',
    ];
    /**
     * @var array
     */
    private $enum = [
        'status'       => [
            1 => 'active',
            2 => 'disabled',
        ],
        'pre_selected' => [
            1 => 'yes',
            2 => 'no',
        ],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Entities\Project', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sales()
    {
        return $this->hasMany('App\Entities\Sale', 'shipping');
    }
}
