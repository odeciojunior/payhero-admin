<?php

namespace Modules\Finances\Exports\Reports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Core\Events\FinancesExportedEvent;
use Modules\Transfers\Services\GetNetStatementService;

class FinanceReportExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithEvents
{
    use Exportable;

    protected $filters;

    protected $user;

    protected $filename;

    protected $getNetStatementService;

    protected $email;

    protected $arrayTotal;

    public function __construct(array $filters, $user, $filename)
    {
        $this->filters = $filters;
        $this->getNetStatementService = new GetNetStatementService();
        $this->user = $user;
        $this->filename = $filename;
        $this->email = !empty($filters["email"]) ? $filters["email"] : $user->email;
        $this->arrayTotal = [];
    }

    public function collection()
    {
        $filtersAndStatement = (new GetNetStatementService())->getFiltersAndStatement($this->filters);
        $filters = $filtersAndStatement["filters"];
        $result = json_decode($filtersAndStatement["statement"]);

        $data = (new GetNetStatementService())->performWebStatement($result, $filters);
        $items = collect($data["items"]);
        unset($data["items"]);
        $this->arrayTotal = $data;

        $items->push([$this->arrayTotal]);

        return $items;
    }

    public function map($row): array
    {
        if (!isset($row->amount)) {
            $totalInPeriod = isset($row[0]["totalInPeriod"]) ? $row[0]["totalInPeriod"] : 0;

            return [
                "",
                "",
                "Saldo no período:",
                'R$ ' . is_string($totalInPeriod) ? $totalInPeriod : number_format($totalInPeriod, 2, ",", "."),
            ];
        }

        $order = collect($row->order);
        $details = collect($row->details);
        $description = str_replace("Venda em: ", "", $details["description"]);

        $data = [
            isset($order["hashId"])
                ? "Transação #" . $order["hashId"] . " (" . $details["description"] . ")"
                : $description,
            $details["status"],
            $row->date,
            'R$ ' . number_format($row->amount, 2, ",", "."),
        ];

        return $data;
    }
    //
    public function headings(): array
    {
        return [
            //finance
            "Razão",
            "Status",
            "Data prevista",
            "Valor",
        ];
    }
    //
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = "A1:D1"; // All headers
                $event->sheet
                    ->getDelegate()
                    ->getStyle($cellRange)
                    ->getFill()
                    ->setFillType("solid")
                    ->getStartColor()
                    ->setRGB("E16A0A");
                $event->sheet
                    ->getDelegate()
                    ->getStyle($cellRange)
                    ->getFont()
                    ->applyFromArray([
                        "color" => ["rgb" => "ffffff"],
                        "size" => 16,
                    ]);

                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                $setGray = false;
                $lastFinance = null;
                for ($row = 2; $row <= $lastRow; $row++) {
                    $currentFinance = $event->sheet
                        ->getDelegate()
                        ->getCellByColumnAndRow(1, $row)
                        ->getValue();
                    if ($currentFinance != $lastFinance && isset($lastFinance)) {
                        $setGray = !$setGray;
                    }
                    if ($setGray) {
                        $event->sheet
                            ->getDelegate()
                            ->getStyle("A" . $row . ":AS" . $row)
                            ->getFill()
                            ->setFillType("solid")
                            ->getStartColor()
                            ->setRGB("e5e5e5");
                    }
                    $lastFinance = $currentFinance;
                }
                event(new FinancesExportedEvent($this->user, $this->filename, $this->email));
            },
        ];
    }
}
