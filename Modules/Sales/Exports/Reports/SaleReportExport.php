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
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize($this->fontSize);
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
