<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $role_id
 * @property string $model_type
 * @property integer $model_id
 * @property Role $role
 */
class ModelHasRoles extends Model
{
    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo('App\Role');
    }
}
