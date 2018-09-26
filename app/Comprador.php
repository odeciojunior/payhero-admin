<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


/**
 * @property integer $id
 * @property string $nome
 * @property string $cpf_cnpj
 * @property string $data_nascimento
 * @property string $email
 * @property string $telefone
 * @property string $cep
 * @property string $pais
 * @property string $estado
 * @property string $cidade
 * @property string $bairro
 * @property string $rua
 * @property string $15
 * @property string $ponto_referencia
 * @property string $password
 * @property string $remember_token
 * @property integer $id_kapsula_cliente
 * @property Venda[] $vendas
 */
class Comprador extends  Authenticatable
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'compradores';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = [
        'nome', 'cpf_cnpj', 'data_nascimento', 'email', 
        'telefone', 'password', 'created_at', 'updated_at', 'remember_token',
        'id_kapsula_cliente'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendas()
    {
        return $this->hasMany('App\Venda', 'comprador');
    }
}
