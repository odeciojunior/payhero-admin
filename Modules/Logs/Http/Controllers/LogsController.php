<?php

namespace Modules\Logs\Http\Controllers;

use App\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class LogsController extends Controller {

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function logs() {

        return view('logs::logs');
    }

    public function dadosLogs(){

        return datatables(
            \DB::table('logs as log')
            ->leftjoin('planos as plano', 'log.plano', '=', 'plano.cod_identificador')
            ->get([
                'log.id',
                'log.id_sessao_log',
                'plano.nome as plano_nome',
                'log.plano',
                'log.evento',
                'log.sistema_operacional',
                'log.navegador',
                'log.hora_acesso',
                'log.horario',
                'log.forward',
                'log.referencia',
                'log.nome',
                'log.email',
                'log.cpf',
                'log.celular',
                'log.cep',
                'log.endereco',
                'log.numero',
                'log.bairro',
                'log.cidade',
                'log.estado',
                'log.valor_frete',
                'log.valor_cupom',
                'log.valor_total',
                'log.erro',
                'log.created_at'
            ])
        )->editColumn('created_at', function ($log) {
            return $log->created_at ? with(new Carbon($log->created_at))->format('d/m/Y H:i:s') : '';
        })->toJson();

    }

}
