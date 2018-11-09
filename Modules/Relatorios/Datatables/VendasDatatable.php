<?php

namespace Modules\Relatorios\Datatables;

use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class VendasDatatable extends DataTable {

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Collaborator $model) {

        $query = \DB::table('vendas as venda')
            ->leftjoin('planos_vendas as plano_venda', 'plano_venda.venda', '=', 'venda.id')
            ->leftjoin('compradores as comprador', 'comprador.id', '=', 'venda.comprador')
            ->leftjoin('planos as plano', 'plano_venda.plano', '=', 'plano.id')
            ->get([
                'venda.id',
                'plano.nome as plano_nome',
                'comprador.nome',
                'venda.meio_pagamento',
                'venda.forma_pagamento',
                'venda.mercado_pago_status',
                'venda.data_inicio',
                'venda.data_finalizada',
                'venda.valor_plano',
        ]);

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->setTableAttribute('class', 'table responsive table-hover')
            ->parameters([
                'dom' => "<'row'<'col-sm-12'tr>>\n\t\t\t<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
                'stateSave' => true,
                'columnFilters' => $this->getFilters(),
                'regexp' => true
            ]);
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
     * @return array
     */
    protected function getColumns()
    {

        $data = [];

        $data += [
            'id' => [
                'data' => 'id',
                'title' => 'id',
                'data_type' => 'text'
            ],
            'plano_nome' => [
                'data' => 'plano_nome',
                'title' => 'plano_nome',
                'data_type' => 'text'
            ],
            'nome' => [
                'data' => 'nome',
                'title' => 'nome',
                'data_type' => 'text'
            ],
            'meio_pagamento' => [
                'data' => 'meio_pagamento',
                'title' => 'meio_pagamento',
                'data_type' => 'text'
            ],
            'forma_pagamento' => [
                'data' => 'forma_pagamento',
                'title' => 'forma_pagamento',
                'data_type' => 'text'
            ],
            'mercado_pago_status' => [
                'data' => 'mercado_pago_status',
                'title' => 'mercado_pago_status',
                'data_type' => 'text'
            ],
            'data_inicio' => [
                'data' => 'data_inicio',
                'title' => 'data_inicio',
                'data_type' => 'text'
            ],
            'data_finalizada' => [
                'data' => 'data_finalizada',
                'title' => 'data_finalizada',
                'data_type' => 'text'
            ],
            'valor_plano' => [
                'data' => 'valor_plano',
                'title' => 'valor_plano',
                'data_type' => 'text'
            ],
            // 'actions' => ['title' => 'acoes', 'data_type' => 'actions', 'orderable' => false],
        ];

        return $data;

    }

}
