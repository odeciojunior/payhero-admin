<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $projeto
 * @property string $nome
 * @property string $descricao
 * @property boolean $tipo
 * @property int $valor
 * @property string $cod_cupom
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Projeto $projeto
 * @property PlanosCupon[] $planosCupons
 */
class Cupom extends Model {

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'cupons';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['projeto', 'nome', 'tipo', 'valor', 'cod_cupom', 'status', 'created_at', 'updated_at', 'deleted_at'];

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
    public function planosCupons()
    {
        return $this->hasMany('App\PlanosCupon', 'cupom');
    }
}
