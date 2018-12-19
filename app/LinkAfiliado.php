<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $afiliado
 * @property integer $plano
 * @property string $parametro
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Afiliado $afiliado
 * @property Plano $plano
 */
class LinkAfiliado extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'links_afiliados';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['afiliado', 'plano', 'parametro', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function afiliado()
    {
        return $this->belongsTo('App\Afiliado', 'afiliado');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plano()
    {
        return $this->belongsTo('App\Plano', 'plano');
    }
}
