<?php

namespace Modules\Reports\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
     * @var int|null
     */
    private $fontSize = 12;

    /**
     * SaleReportExport constructor.
     * @param $collection
     * @param $headings
     * @param null $fontSize
     */
    public function __construct($collection, $headings, $fontSize = null)
    {
        $this->collection = $collection; // Collection
        $this->headings   = $headings; // Array
        if ($fontSize !== null) // Number
            $this->fontSize = $fontSize;
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
                $cellRange = 'A1:AS1'; // All headers
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
                            ->getStyle('A' . $row . ':AS' . $row)
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
        return $this->headings;
    }
}
