<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $afiliado
 * @property string $descricao
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Afiliado $afiliado
 */
class Campanha extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['afiliado', 'descricao', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function afiliado()
    {
        return $this->belongsTo('App\Afiliado', 'afiliado');
    }
}
