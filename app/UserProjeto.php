<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user
 * @property int $projeto
 * @property int $empresa
 * @property string $tipo
 * @property string $tipo_remuneracao
 * @property string $valor_remuneracao
 * @property boolean $responsavel_frete
 * @property boolean $permissao_acesso
 * @property boolean $permissao_editar
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $status
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
     * @var array
     */
    protected $fillable = [ 'user', 'projeto', 'empresa', 'tipo', 'tipo_remuneracao', 'valor_remuneracao', 'responsavel_frete', 'permissao_acesso', 'permissao_editar', 'created_at', 'updated_at', 'deleted_at', 'status'];

    protected $guarded = ['id'];
}
