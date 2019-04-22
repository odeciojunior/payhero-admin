<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $descricao
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Brinde[] $brindes
 */
class TipoBrinde extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['descricao', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function brindes()
    {
        return $this->hasMany('App\Brinde', 'tipo_brinde');
    }
}
