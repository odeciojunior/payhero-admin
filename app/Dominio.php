<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $projeto
 * @property string $dominio
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Empresa $empresa
 * @property Projeto $projeto
 * @property Layout $layout
 * @property PerguntasFrequente[] $perguntasFrequentes
 */
class Dominio extends Model
{
    /**
     * @var array
     */
    protected $fillable = [ 'projeto', 'dominio','ip_dominio','status', 'created_at', 'updated_at', 'deleted_at'];

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
    public function perguntasFrequentes()
    {
        return $this->hasMany('App\PerguntasFrequente', 'dominio');
    }
}
