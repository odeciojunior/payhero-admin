<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $cnpj
 * @property string $cep
 * @property string $municipio
 * @property string $logradouro
 * @property string $complemento
 * @property string $bairro
 * @property string $numero
 * @property string $ultima_atualizacao
 * @property string $uf
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $status
 * @property string $razao_social
 * @property string $nome_fantasia
 * @property string $banco
 * @property string $agencia
 * @property string $agencia_digito
 * @property string $conta
 * @property string $conta_digito
 * @property DadosHotzapp[] $dadosHotzapps
 * @property Dominio[] $dominios
 * @property Plano[] $planos
 * @property Produto[] $produtos
 * @property Projeto[] $projetos
 * @property UsersEmpresa[] $usersEmpresas
 */
class Empresa extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['cnpj', 'cep', 'municipio', 'logradouro', 'complemento', 'bairro', 'numero', 'ultima_atualizacao', 'uf', 'created_at', 'updated_at', 'deleted_at', 'status', 'razao_social', 'nome_fantasia', 'banco', 'agencia', 'agencia_digito', 'conta', 'conta_digito'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dadosHotzapps()
    {
        return $this->hasMany('App\DadosHotzapp', 'empresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dominios()
    {
        return $this->hasMany('App\Dominio', 'empresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planos()
    {
        return $this->hasMany('App\Plano', 'empresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function produtos()
    {
        return $this->hasMany('App\Produto', 'empresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projetos()
    {
        return $this->hasMany('App\Projeto', 'empresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usersEmpresas()
    {
        return $this->hasMany('App\UsersEmpresa', 'empresa');
    }
}
