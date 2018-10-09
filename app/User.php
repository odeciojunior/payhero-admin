<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticable;
use Spatie\Permission\Traits\HasRoles;

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
 * @property UsersEmpresa[] $usersEmpresas
 */

class User extends Authenticable
{
    use Notifiable;
    use HasRoles;

    /**
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'remember_token', 'created_at', 'updated_at', 'data_nascimento', 'celular', 'cpf', 'cep', 'pais', 'estado', 'cidade', 'bairro', 'logradouro', 'numero', 'complemento', 'telefone2', 'telefone1', 'referencia', 'foto', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usersEmpresas()
    {
        return $this->hasMany('App\UsersEmpresa', 'user');
    }
}
