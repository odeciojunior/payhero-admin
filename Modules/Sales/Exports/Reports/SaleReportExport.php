<?php

namespace Modules\Sales\Exports\Reports;

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
    private $headings = [];
    /**
     * @var array
     */
    private $collection = [];

    /**
     * SaleReportExport constructor.
     * @param $collection
     * @param $headings
     */
    public function __construct($collection, $headings)
    {
        $this->collection = $collection; // Collection
        $this->headings   = $headings; // Array
    }

    /**
     * @return array|\Illuminate\Support\Collection
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
                    $currentSale = $this->collection()->get($row - 1)['sale_code'];
                    if($setGray){
                            $event->sheet->getDelegate()
                                ->getStyle('A' . $row . ':AR' . $row)
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
        return $this->headings;
    }
}
