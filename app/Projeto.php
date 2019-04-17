<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $foto
 * @property string $visibilidade
 * @property boolean $status
 * @property string $nome
 * @property string $descricao
 * @property string $created_at
 * @property string $updated_at
 */
class Projeto extends Model
{
    use SoftDeletes;
    /**
     * @var array
     */
    protected $fillable = [ 'foto', 'visibilidade', 'status', 'nome', 'descricao','url_pagina','sms_status', 'descricao_fatura','porcentagem_afiliados','afiliacao_automatica','shopify_id', 'created_at', 'updated_at','frete','frete_fixo','valor_frete','responsavel_frete','transportadora','qtd_parcelas','parcelas_sem_juros','duracao_cookie','url_cookies_checkout','contato'];

    protected $dates = [ 'deleted_at' ];
}
