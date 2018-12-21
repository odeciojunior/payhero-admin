<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $user
 * @property int $projeto
 * @property int $empresa
 * @property string $tipo_parceiro
 * @property string $tipo_remuneracao
 * @property string $valor_remuneracao
 * @property boolean $responsavel_frete
 * @property boolean $permissao_acesso
 * @property boolean $permissao_editar
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Empresa $empresa
 * @property Projeto $projeto
 * @property User $user
 */
class UserProjeto extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'users_projetos';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['user', 'projeto', 'status', 'empresa', 'tipo', 'tipo_remuneracao', 'valor_remuneracao', 'responsavel_frete', 'permissao_acesso', 'permissao_editar', 'created_at', 'updated_at', 'deleted_at'];

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
    public function projeto()
    {
        return $this->belongsTo('App\Projeto', 'projeto');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user');
    }
}
