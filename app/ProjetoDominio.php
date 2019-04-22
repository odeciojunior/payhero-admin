<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $dominio
 * @property int $projeto
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Dominio $dominio
 * @property Projeto $projeto
 */
class ProjetoDominio extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'projetos_dominios';

    /**
     * @var array
     */
    protected $fillable = ['dominio', 'projeto', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dominio()
    {
        return $this->belongsTo('App\Dominio', 'dominio');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projeto()
    {
        return $this->belongsTo('App\Projeto', 'projeto');
    }
}
