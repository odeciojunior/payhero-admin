<?php

namespace Modules\Relatorios\DataTables;

use App\Venda;
use Carbon\Carbon;
use Yajra\DataTables\Services\DataTable;

class VendasDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
        ->editColumn('data_inicio', function ($venda) {
            return $venda->data_inicio ? with(new Carbon($venda->data_inicio))->format('d/m/Y H:i:s') : '';
        })
        ->editColumn('data_finalizada', function ($venda) {
            return $venda->data_finalizada ? with(new Carbon($venda->data_finalizada))->format('d/m/Y H:i:s') : '';
        })
        ->editColumn('forma_pagamento', function ($venda) {
            if($venda->forma_pagamento == 'cartao_credito') 
                return 'Cartão de crédito';
            if($venda->forma_pagamento == 'boleto') 
                return 'Boleto';
            return $venda->forma_pagamento;
        })
        ->editColumn('status', function ($venda) {
            if($venda->status == 'paid')
                return 'Aprovada';
            if($venda->status == 'refused')
                return 'Rejeitada';
            if($venda->status == 'waiting_payment')
                return 'Aguardando pagamento';
            if($venda->status == 'pending')
                return 'Pendente';
            if($venda->status == '')
                return '- - -';
            return $venda->status;
        })
        ->addColumn('detalhes', function ($venda) {
            $buttons = "<button class='btn btn-sm btn-outline btn-primary detalhes_venda' venda='".$venda->id."' data-target='#modal_detalhes' data-toggle='modal' type='button'>
                            Detalhes
                        </button>";
            if($venda->status == 'paid'){
                $buttons .= "<button class='btn btn-sm btn-outline btn-danger estornar_venda' venda='".$venda->id."' data-target='#modal_estornar' data-toggle='modal' type='button'>
                                Estornar
                             </button>";
            }
            return $buttons;
        })
        ->rawColumns(['detalhes']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Venda $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Venda $vendas) {

        $query = $vendas->newQuery()
            ->leftjoin('planos_vendas as plano_venda', 'plano_venda.venda', '=', 'vendas.id')
            ->leftjoin('compradores as comprador', 'comprador.id', '=', 'vendas.comprador')
            ->leftjoin('planos as plano', 'plano_venda.plano', '=', 'plano.id')
            ->select([
                'vendas.id',
                'plano.nome as plano_nome',
                'comprador.nome',
                'vendas.forma_pagamento',
                'vendas.pagamento_status as status',
                'vendas.data_inicio',
                'vendas.data_finalizada',
                'vendas.valor_plano',
        ]);

        if(!\Auth::user()->hasRole('administrador geral')){
            $query = $query->where('proprietario',\Auth::user()->id);
        }

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html() {

        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                'order' => [ [ 0, 'desc' ] ],
                'stateSave' => false,
                'regexp' => true,
                'lengthChange' => false,
                'language'=> [
                    'sProcessing'=>    'Procesando...',
                    'lengthMenu'=> 'Apresentando _MENU_ registros por página',
                    'zeroRecords'=> 'Nenhum registro encontrado',
                    'info'=> 'Apresentando página _PAGE_ de _PAGES_',
                    'infoEmpty'=> 'Nenhum registro encontrado',
                    'infoFiltered'=> '(filtrado por _MAX_ registros)',
                    'sSearch'=>        'Procurar :',
                    'sUrl'=>           '',
                    'sInfoThousands'=>  ',',
                    'sLoadingRecords'=> 'Carregando...',
                    'oPaginate'=> [
                        'sFirst'=>    'Primeiro',
                        'sLast'=>    'Último',
                        'sNext'=>    'Próximo',
                        'sPrevious'=> 'Anterior',
                    ]
                ],
                'drawCallback' =>  "function() {
                    var id_venda = '';

                    $('.detalhes_venda').unbind('click');

                    $('.detalhes_venda').on('click', function() {

                        var venda = $(this).attr('venda');
    
                        $('#modal_venda_titulo').html('Detalhes da venda #' + venda);
    
                        $('#modal_detalhes_body').html('<h5 style=".'"'.'width:100%; text-align: center='.'"'.">Carregando..</h5>');

                        var data = { id_venda : venda };

                        $.post('/relatorios/venda/detalhe', data)
                        .then( function(response, status){

                            $('#modal_venda_body').html(response);
                        });
                    });

                    $('.estornar_venda').unbind('click');

                    $('.estornar_venda').on('click', function() {

                        id_venda = $(this).attr('venda');

                        $('#modal_estornar_titulo').html('Estornar venda #' + id_venda + ' ?');
                        $('#modal_estornar_body').html('');

                    });

                    $('.bt_estornar_venda').unbind('click');

                    $('.bt_estornar_venda').on('click', function() {

                        $('#modal_estornar_body').html('<h5 style=".'"'.'width:100%; text-align: center='.'"'.">Realizando estorno...</h5>');

                        $.ajax({
                            method: 'POST',
                            url: '/relatorios/venda/estornar',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name=".'"'.'csrf-token'.'"'."]').attr('content')
                            },
                            data: { id_venda : id_venda },
                            error: function(){
                                //
                            },
                            success: function(data){
                                if(data.sucesso){
                                    $('#modal_estornar_body').html('<h5 style=".'"'.'width:100%; text-align: center='.'"'.">Estorno realizado com sucesso</h5>');
                                }
                                else{
                                    alert('Ocorreu algum erro ao realizar o estorno');
                                }
                            }
                        });
                    });
                    
                }",
        ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns() {

        return [
            'id' => [
                'name' => 'id',
                'data' => 'id',
                'title' => 'Venda',
                'searchable' => true,
                'orderable' => true,
                'data_type' => 'text',
                'filter_type' => 'text'
            ],
            'plano_nome' => [
                'name' => 'plano_nome',
                'data' => 'plano_nome',
                'title' => 'Plano',
                'searchable' => true,
                'orderable' => false,
                'data_type' => 'text',
                'filter_type' => 'text'
            ],
            'nome' => [
                'name' => 'nome',
                'data' => 'nome',
                'title' => 'Comprador',
                'searchable' => true,
                'orderable' => false,
                'data_type' => 'text',
                'filter_type' => 'text'
            ],
            'forma_pagamento' => [
                'name' => 'forma_pagamento',
                'data' => 'forma_pagamento',
                'title' => 'Forma',
                'searchable' => true,
                'orderable' => false,
                'data_type' => 'text',
                'filter_type' => 'text'
            ],
            'status' => [
                'name' => 'status',
                'data' => 'status',
                'title' => 'Status',
                'searchable' => true,
                'orderable' => false,
                'data_type' => 'text',
                'filter_type' => 'text'
            ],
            'data_inicio' => [
                'name' => 'data_inicio',
                'data' => 'data_inicio',
                'title' => 'Data',
                'searchable' => true,
                'orderable' => false,
                'data_type' => 'text',
                'filter_type' => 'text'
            ],
            'data_finalizada' => [
                'name' => 'data_finalizada',
                'data' => 'data_finalizada',
                'title' => 'Pagamento',
                'searchable' => true,
                'orderable' => false,
                'data_type' => 'text',
                'filter_type' => 'text'
            ],
            'valor_plano' => [
                'name' => 'valor_plano',
                'data' => 'valor_plano',
                'title' => 'Valor',
                'searchable' => true,
                'orderable' => false,
                'data_type' => 'text',
                'filter_type' => 'text'
            ],
            'detalhes' => [
                'name' => 'detalhes',
                'data' => 'detalhes',
                'title' => 'Detalhes',
                'searchable' => false,
                'orderable' => false,
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getFilters(){

        $columnFilters = [];

        $columnFilters[] = [
            'column_number' => 0,
            'filter_type' => 'text'
        ];
        $columnFilters[] = [
            'column_number' => 1,
            'filter_type' => 'text'
        ];
        $columnFilters[] = [
            'column_number' => 2,
            'filter_type' => 'text'
        ];
        $columnFilters[] = [
            'column_number' => 3,
            'filter_type' => 'text'
        ];
        $columnFilters[] = [
            'column_number' => 4,
            'filter_type' => 'text'
        ];
        $columnFilters[] = [
            'column_number' => 5,
            'filter_type' => 'text'
        ];
        $columnFilters[] = [
            'column_number' => 6,
            'filter_type' => 'text'
        ];

        return $columnFilters;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename() {

        return 'Vendas_' . date('YmdHis');
    }
}
