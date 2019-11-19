<?php

namespace Modules\Sales\Exports\Reports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class SaleReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
     * @var array
     */
    private $collection = [];

    /**
     * SaleReportExport constructor.
     * @param $collection
     */
    public function __construct($collection)
    {
        $this->collection = $collection; // Collection
    }

    /**
     * @return array|Collection
     */
    public function collection()
    {
        return $this->collection;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $cellRange = 'A1:AR1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)
                    ->getFill()
                    ->setFillType('solid')
                    ->getStartColor()
                    ->setRGB('E16A0A');
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->applyFromArray([
                    'color' => ['rgb' => 'ffffff'],
                    'size' => 16
                ]);

                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                $setGray = false;
                $lastSale = null;
                for ($row = 2; $row <= $lastRow; $row++) {
                    $currentSale = $event->sheet->getDelegate()->getCellByColumnAndRow(1, $row)->getValue();
                    if ($currentSale != $lastSale && isset($lastSale)) {
                        $setGray = !$setGray;
                    }
                    if($setGray){
                            $event->sheet->getDelegate()
                                ->getStyle('A' . $row . ':AR' . $row)
                                ->getFill()
                                ->setFillType('solid')
                                ->getStartColor()
                                ->setRGB('e5e5e5');
                    }
                    $lastSale = $currentSale;
                }
            },
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            //sale
            'Código da Venda',
            'Pedido do Shopify',
            'Forma de Pagamento',
            'Número de Parcelas',
            'Bandeira do Cartão',
            'Link do Boleto',
            'Linha Digitavel do Boleto',
            'Data de Vencimento do Boleto',
            'Data Inicial do Pagamento',
            'Data Final do Pagamento',
            'Status',
            'Valor Total Venda',
            'Frete',
            'Valor do Frete',
            'Taxas',
            'Comissão',
            //plan
            'Projeto',
            'Plano',
            'Preço do Plano',
            'Código dos produtos',
            'Produto',
            'Id do Shopify',
            'Id da Variante do Shopify',
            'Quantidade dos Produtos',
            'SKU',
            //client
            'Nome do Cliente',
            'Telefone do Cliente',
            'Email do Cliente',
            'Documento',
            'Endereço',
            'Número',
            'Complemento',
            'Bairro',
            'Cep',
            'Cidade',
            'Estado',
            'País',
            //track
            'src',
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_term',
            'utm_content',
            'utm_perfect',
        ];
    }
}
