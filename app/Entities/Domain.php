<?php

namespace App\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $project
 * @property string $name
 * @property string $status
 * @property string $domain_ip
 * @property string $sendgrid_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project_id
 */
class Domain extends Model
{
    use SoftDeletes;
    use FoxModelTrait;
    protected $dates = ['deleted_at'];
    /**
     * @var array
     */
    protected $fillable = [
        'project_id',
        'name',
        'status',
        'domain_ip',
        'sendgrid_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * @var array
     */
    private $enum = [
        'status' => [
            1 => 'pending',
            2 => 'analyzing',
            3 => 'approved',
            4 => 'refused',
        ],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Entities\Project');
    }
}
