<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $projeto
 * @property string $nome
 * @property string $cod_pixel
 * @property string $plataforma
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Projeto $projeto
 * @property PlanosPixel[] $planosPixels
 */
class Pixel extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['projeto', 'nome', 'cod_pixel', 'plataforma', 'status','campanha', 'created_at', 'updated_at', 'deleted_at'];

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
    public function planosPixels()
    {
        return $this->hasMany('App\PlanosPixel', 'pixel');
    }
}
