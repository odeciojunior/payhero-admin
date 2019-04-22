<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $user_convite
 * @property int $user_convidado
 * @property string $email_convidado
 * @property string $status
 * @property string $data_cadastro
 * @property string $data_expiracao
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Convite extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['user_convite', 'user_convidado', 'email_convidado', 'status', 'empresa', 'parametro', 'data_cadastro', 'data_expiracao', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_convidado');
    }

}
