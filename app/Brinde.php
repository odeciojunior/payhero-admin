<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $tipo_brinde
 * @property string $descricao
 * @property string $titulo
 * @property string $foto
 * @property string $link
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property TipoBrinde $tipoBrinde
 * @property PlanosBrinde[] $planosBrindes
 */
class Brinde extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['tipo_brinde', 'descricao', 'titulo', 'foto', 'link', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipoBrinde()
    {
        return $this->belongsTo('App\TipoBrinde', 'tipo_brinde');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planosBrindes()
    {
        return $this->hasMany('App\PlanosBrinde', 'brinde');
    }
}
