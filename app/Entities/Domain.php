<?php

namespace App\Entities;

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
 * @property Project $project
 */
class Domain extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    /**
     * @var array
     */
    protected $fillable = [
        'project',
        'name', 'status',
        'domain_ip',
        'sendgrid_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Entities\Project', 'project');
    }
}
