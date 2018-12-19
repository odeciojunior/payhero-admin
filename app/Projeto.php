<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $empresa
 * @property string $foto
 * @property string $visibilidade
 * @property boolean $status
 * @property string $nome
 * @property string $descricao
 * @property string $created_at
 * @property string $updated_at
 * @property Empresa $empresa
 */
class Projeto extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['empresa', 'foto', 'visibilidade', 'status', 'nome', 'descricao','url_pagina','sms_status', 'descricao_fatura','porcentagem_afiliados', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empresa()
    {
        return $this->belongsTo('App\Empresa', 'empresa');
    }
}
