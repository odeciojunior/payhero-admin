<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $empresa
 * @property string $descricao
 * @property string $link
 * @property string $created_at
 * @property string $updated_at
 * @property Empresa $empresa
 * @property Plano[] $planos
 */
class DadosHotZapp extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'dados_hotzapp';

    /**
     * @var array
     */
    protected $fillable = ['empresa', 'descricao', 'link', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empresa()
    {
        return $this->belongsTo('App\Empresa', 'empresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planos()
    {
        return $this->hasMany('App\Plano', 'hotzapp_dados');
    }
}
