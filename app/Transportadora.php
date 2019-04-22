<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $site
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Plano[] $planos
 */
class Transportadora extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'site', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planos()
    {
        return $this->hasMany('App\Plano', 'transportadora');
    }
}
