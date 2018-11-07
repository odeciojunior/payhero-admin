<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $layout
 * @property int $empresa
 * @property string $dominio
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Empresa $empresa
 * @property Layout $layout
 * @property PerguntasFrequente[] $perguntasFrequentes
 */
class Dominio extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['layout', 'empresa', 'dominio', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empresa()
    {
        return $this->belongsTo('App\Empresa', 'empresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function layout()
    {
        return $this->belongsTo('App\Layout', 'layout');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function perguntasFrequentes()
    {
        return $this->hasMany('App\PerguntasFrequente', 'dominio');
    }
}
