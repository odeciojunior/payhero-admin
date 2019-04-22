<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $projeto
 * @property string $descricao
 * @property string $tipo
 * @property string $material
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Projeto $projeto
 */
class MaterialExtra extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'materiais_extras';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['projeto', 'descricao', 'tipo', 'material', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projeto()
    {
        return $this->belongsTo('App\Projeto', 'projeto');
    }
}
