<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $layout
 * @property int $projeto
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Layout $layout
 * @property Projeto $projeto
 */
class ProjetoLayout extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'projetos_layouts';

    /**
     * @var array
     */
    protected $fillable = ['layout', 'projeto', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function layout()
    {
        return $this->belongsTo('App\Layout', 'layout');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projeto()
    {
        return $this->belongsTo('App\Projeto', 'projeto');
    }
}
