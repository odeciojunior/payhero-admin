<?php

namespace Modules\Usuario\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $status
 * @property string $emaill
 * @property string $cep
 * @property string $municipio
 * @property string $logradouro
 * @property string $cod_atividade
 * @property string $data_situacao
 * @property string $situacao
 * @property string $abertura
 * @property string $complemento
 * @property string $bairro
 * @property string $numero
 * @property string $ultima_atualizacao
 * @property string $fantasia
 * @property string $capital_social
 * @property string $atividade_principal
 * @property string $nome
 * @property string $uf
 * @property string $telefone
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property UsersEmpresa[] $usersEmpresas
 */
class Empresa extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['status', 'emaill', 'cep', 'municipio', 'logradouro', 'cod_atividade', 'data_situacao', 'situacao', 'abertura', 'complemento', 'bairro', 'numero', 'ultima_atualizacao', 'fantasia', 'capital_social', 'atividade_principal', 'nome', 'uf', 'telefone', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usersEmpresas()
    {
        return $this->hasMany('App\UsersEmpresa', 'empresa');
    }
}
