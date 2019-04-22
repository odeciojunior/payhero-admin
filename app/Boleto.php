<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $venda
 * @property string $linha_digitavel
 * @property string $link
 * @property string $created_at
 * @property string $updated_at
 * @property Venda $venda
 */
class Boleto extends Model
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
    protected $fillable = ['venda', 'linha_digitavel', 'link', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function venda()
    {
        return $this->belongsTo('App\Venda', 'venda');
    }
}
