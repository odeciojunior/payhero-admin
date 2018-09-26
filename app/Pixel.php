<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nome
 * @property string $cod_pixel
 * @property string $plataforma
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property PlanosPixel[] $planosPixels
 */
class Pixel extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['nome', 'cod_pixel', 'plataforma', 'status', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planosPixels()
    {
        return $this->hasMany('App\PlanosPixel', 'pixel');
    }
}
