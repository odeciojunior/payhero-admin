<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property int $project_id
 * @property int $user_id
 * @property string $token_webhook
 * @property string $token_api
 * @property string $token_logistics
 * @property string $created_at
 * @property string $updated_at
 * @property Project $project
 * @property User $user
 */
class NotazzIntegration extends Model
{
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
        'project_id',
        'user_id',
        'token_webhook',
        'token_api',
        'token_logistics',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Entities\Project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Entities\User');
    }
}
