<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $plano
 * @property string $id_zenvia
 * @property string $para
 * @property string $mensagem
 * @property string $data
 * @property string $recebida_de
 * @property string $status
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property string $evento
 * @property string $tipo
 * @property Plano $plano
 */
class MensagemSms extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'mensagens_sms';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['plano', 'id_zenvia', 'para', 'mensagem', 'data', 'recebida_de', 'status', 'user', 'deleted_at', 'created_at', 'updated_at', 'evento', 'tipo'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plano()
    {
        return $this->belongsTo('App\Plano', 'plano');
    }
}
