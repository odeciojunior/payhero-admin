<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property string $created_at
 * @property string $updated_at
 * @property string $data_nascimento
 * @property string $celular
 * @property string $cpf
 * @property string $cep
 * @property string $pais
 * @property string $estado
 * @property string $cidade
 * @property string $bairro
 * @property string $logradouro
 * @property string $numero
 * @property string $complemento
 * @property string $telefone2
 * @property string $telefone1
 * @property string $referencia
 * @property string $foto
 * @property string $deleted_at
 * @property string $score
 * @property int $sms_zenvia_qtd
 * @property string $taxa_porcentagem
 * @property string $taxa_transacao
 * @property float $saldo
 * @property string $foxcoin
 * @property string $email_qtd
 * @property string $ligacao_qtd
 * @property Afiliado[] $afiliados
 * @property ComprasUsuario[] $comprasUsuarios
 * @property Convite[] $convites
 * @property Convite[] $convites
 * @property Empresa[] $empresas
 * @property IntegracoesShopify[] $integracoesShopifies
 * @property Mensagen[] $mensagens
 * @property MensagensSm[] $mensagensSms
 * @property Produto[] $produtos
 * @property SolicitacoesAfiliaco[] $solicitacoesAfiliacoes
 * @property UsersProjeto[] $usersProjetos
 * @property Venda[] $vendas
 */
class User extends Authenticable
{
    use Notifiable;
    use HasRoles;
    use HasApiTokens;

    /**
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'remember_token', 'created_at', 'updated_at', 'data_nascimento', 'celular', 'cpf', 'cep', 'pais', 'estado', 'cidade', 'bairro', 'logradouro', 'numero', 'complemento', 'telefone2', 'telefone1', 'referencia', 'foto', 'deleted_at', 'score', 'sms_zenvia_qtd', 'taxa_porcentagem', 'taxa_transacao', 'saldo', 'foxcoin', 'email_qtd', 'ligacao_qtd','dias_antecipacao'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function afiliados()
    {
        return $this->hasMany('App\Afiliado', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comprasUsuarios()
    {
        return $this->hasMany('App\ComprasUsuario', 'comprador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function convites()
    {
        return $this->hasMany('App\Convite', 'user_convidado');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function empresas()
    {
        return $this->hasMany('App\Empresa', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function integracoesShopifies()
    {
        return $this->hasMany('App\IntegracoesShopify', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mensagens()
    {
        return $this->hasMany('App\Mensagen', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mensagensSms()
    {
        return $this->hasMany('App\MensagensSm', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function produtos()
    {
        return $this->hasMany('App\Produto', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function solicitacoesAfiliacoes()
    {
        return $this->hasMany('App\SolicitacoesAfiliaco', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usersProjetos()
    {
        return $this->hasMany('App\UsersProjeto', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendas()
    {
        return $this->hasMany('App\Venda', 'proprietario');
    }
}
