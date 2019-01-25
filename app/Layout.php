<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $projeto
 * @property string $descricao
 * @property string $logo
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $formato_logo
 * @property Projeto $projeto
 * @property Plano[] $planos
 */
class Layout extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['projeto', 'descricao', 'logo', 'created_at', 'updated_at', 'deleted_at', 'formato_logo'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projeto()
    {
        return $this->belongsTo('App\Projeto', 'projeto');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planos()
    {
        return $this->hasMany('App\Plano', 'layout');
    }
}
