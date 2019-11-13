<?php

namespace Modules\Trackings\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class TrackingsReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
     * @return array
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
                $cellRange = 'A1:R1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)
                    ->getFill()
                    ->setFillType('solid')
                    ->getStartColor()
                    ->setRGB('3e8ef7');
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->applyFromArray([
                    'color' => ['rgb' => 'ffffff'],
                    'size' => 16
                ]);

                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                $setGray = false;
                $lastSale = null;
                for ($row = 2; $row <= $lastRow; $row++) {
                    $currentSale = $this->collection()->get($row - 1)['sale'];
                    if($setGray){
                        $event->sheet->getDelegate()
                            ->getStyle('A' . $row . ':R' . $row)
                            ->getFill()
                            ->setFillType('solid')
                            ->getStartColor()
                            ->setRGB('e5e5e5');
                    }
                    if ($currentSale != $lastSale && isset($lastSale)) {
                        $setGray = !$setGray;
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
            'Código da Venda',
            'Código do Rastreio',
            'Código do Produto',
            'Produto',
            'Quantidade',
            'SKU',
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
        ];
    }
}
