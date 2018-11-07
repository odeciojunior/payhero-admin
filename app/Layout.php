<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $descricao
 * @property string $logo
 * @property string $logo2
 * @property string $estilo
 * @property string $cor1
 * @property string $cor2
 * @property string $botao
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Dominio[] $dominios
 * @property Plano[] $planos
 */
class Layout extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['descricao', 'logo', 'logo2', 'estilo', 'cor1', 'cor2', 'botao', 'created_at', 'updated_at', 'deleted_at'];

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
