<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user
 * @property string $nome_fantasia
 * @property string $cnpj
 * @property string $cep
 * @property string $municipio
 * @property string $logradouro
 * @property string $complemento
 * @property string $bairro
 * @property string $numero
 * @property string $uf
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $banco
 * @property string $agencia
 * @property string $agencia_digito
 * @property string $conta
 * @property string $conta_digito
 * @property string $country
 * @property string $statement_descriptor
 * @property string $shortened_descriptor
 * @property string $business_website
 * @property string $support_email
 * @property string $support_telephone
 * @property User $user
 * @property Afiliado[] $afiliados
 * @property Convite[] $convites
 * @property DadosHotzapp[] $dadosHotzapps
 * @property Plano[] $planos
 * @property Transaco[] $transacoes
 * @property UsersProjeto[] $usersProjetos
 */
class Empresa extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user', 'nome_fantasia', 'cnpj', 'cep', 'municipio', 'logradouro', 'complemento', 'bairro', 'numero', 'uf', 'created_at', 'updated_at', 'deleted_at', 'banco', 'agencia', 'agencia_digito', 'conta', 'conta_digito', 'country', 'statement_descriptor', 'shortened_descriptor', 'business_website', 'support_email', 'support_telephone'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afiliados()
    {
        return $this->hasMany('App\Afiliado', 'empresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function convites()
    {
        return $this->hasMany('App\Convite', 'empresa');
    }

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
    public function planos()
    {
        return $this->hasMany('App\Plano', 'empresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transacoes()
    {
        return $this->hasMany('App\Transaco', 'empresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usersProjetos()
    {
        return $this->hasMany('App\UsersProjeto', 'empresa');
    }
}
