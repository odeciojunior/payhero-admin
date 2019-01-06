<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
    /**
     * @var array
     */
    protected $fillable = [ 'foto', 'visibilidade','empresa', 'status', 'nome', 'descricao','url_pagina','sms_status', 'descricao_fatura','porcentagem_afiliados', 'created_at', 'updated_at'];

}
