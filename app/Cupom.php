<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $nome
 * @property string $descrica
 * @property boolean $tipo
 * @property int $valor
 * @property string $cod_cupom
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property PlanosCupon[] $planosCupons
 */
class Cupom extends Model
{
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
    protected $fillable = ['nome', 'descrica', 'tipo', 'valor', 'cod_cupom', 'status', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planosCupons()
    {
        return $this->hasMany('App\PlanosCupon', 'cupom');
    }
}
