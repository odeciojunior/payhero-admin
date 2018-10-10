<?php

namespace Modules\Logs\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $id_sessao_log
 * @property string $plano
 * @property string $evento
 * @property string $user_agent
 * @property string $hora_acesso
 * @property string $hora_encerramento
 * @property string $hora_submit
 * @property string $forward
 * @property string $referencia
 * @property string $nome
 * @property string $email
 * @property string $cpf
 * @property string $celular
 * @property string $entrega
 * @property string $cep
 * @property string $endereco
 * @property string $numero
 * @property string $bairro
 * @property string $cidade
 * @property string $estado
 * @property string $valor_frete
 * @property string $valor_cupom
 * @property string $valor_total
 * @property boolean $numero_cartao
 * @property boolean $nome_cartao
 * @property boolean $cpf_cartao
 * @property boolean $mes_cartao
 * @property boolean $ano_cartao
 * @property boolean $codigo_cartao
 * @property boolean $parcelamento
 * @property string $erro
 * @property string $coockies
 */

class Log extends Model
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
    protected $fillable = [
        'id_sessao_log',
        'plano',
        'evento',
        'user_agent',
        'sistema_operacional',
        'navegador',
        'hora_acesso',
        'horario',
        'forward',
        'referencia',
        'nome',
        'email',
        'cpf',
        'celular',
        'entrega',
        'cep',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'estado', 
        'valor_frete', 
        'valor_cupom', 
        'valor_total', 
        'numero_cartao', 
        'nome_cartao', 
        'cpf_cartao', 
        'mes_cartao', 
        'ano_cartao', 
        'codigo_cartao', 
        'parcelamento', 
        'erro', 
        'coockies',
    ];

}
