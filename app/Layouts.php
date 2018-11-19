<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $projeto
 * @property string $descricao
 * @property string $logo
 * @property string $estilo
 * @property string $cor1
 * @property string $cor2
 * @property string $botao
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Projeto $projeto
 * @property Dominio[] $dominios
 * @property Plano[] $planos
 */
class Layouts extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['projeto', 'descricao', 'logo', 'estilo', 'cor1', 'cor2', 'botao', 'created_at', 'updated_at', 'deleted_at'];

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
    public function dominios()
    {
        return $this->hasMany('App\Dominio', 'layout');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planos()
    {
        return $this->hasMany('App\Plano', 'layout');
    }
}
