<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property integer $cupom
 * @property int $projeto
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Cupon $cupon
 * @property Projeto $projeto
 */
class ProjetoCupom extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'projetos_cupons';

    /**
     * @var array
     */
    protected $fillable = ['cupom', 'projeto', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cupon()
    {
        return $this->belongsTo('App\Cupon', 'cupom');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projeto()
    {
        return $this->belongsTo('App\Projeto', 'projeto');
    }
}
