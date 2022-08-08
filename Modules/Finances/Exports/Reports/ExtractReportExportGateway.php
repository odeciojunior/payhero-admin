<?php

namespace Modules\Finances\Exports\Reports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Core\Entities\Transaction;
use Modules\Core\Events\SalesExportedEvent;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SaleService;
use Vinkla\Hashids\Facades\Hashids;

class ExtractReportExportGateway implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    use Exportable;

    private $transfers;
    private $fileName;
    private $email;

    public function __construct($fileName, $transfers)
    {
        $this->transfers = $transfers;
        $this->fileName = $fileName;
        $this->email = auth()->user()->email;
    }

    public function collection()
    {
        return $this->transfers;
    }

    public function map($row): array
    {
        $transfer = $row;

        $type = $transfer->type_enum == 2 ? "-" : "";
        $sale = (new SaleService())->getSaleWithDetails(hashids_encode($transfer->sale_id, "sale_id"));
        return [
            "transfers_id" => "#" . hashids_encode($transfer->id),
            "type" => $this->getType($transfer->type_enum),
            "value" => 'R$' . number_format(intval($type . $transfer->value) / 100, 2, ",", "."),
            "reason" => $this->getReason(),
            "transfer_date" => $transfer->created_at->format("d/m/Y"),
            "is_owner" =>
                $transfer->transaction_type == Transaction::TYPE_PRODUCER || is_null($transfer->transaction_type)
                    ? "SIM"
                    : "NAO",
            "anticipation_id" => $this->getAnticipatedCode() ?? "Nao Possui",
            // SALE
            "sale_id" => "#" . hashids_encode($transfer->sale_id, "sale_id"),
            "subTotal" => $sale->details->subTotal,
            "total" => $sale->details->total,
            "sale_date" => !empty($transfer->transaction)
                ? Carbon::parse($transfer->transaction->sale->start_date)->format("d/m/Y")
                : "",
            "flag" => !empty($sale->flag) ? $sale->flag : $sale->present()->getPaymentFlag(),
            "status_name" => $sale->present()->getStatus($sale->status),
            "cashback_value" =>
                $sale->payment_method != 4
                    ? FoxUtils::formatMoney($sale->cashback->value / 100)
                    : FoxUtils::formatMoney(0),
            "automatic_discount" => $sale->details->automatic_discount ?? 0,
            "discount" => $sale->details->discount,
            "taxaDiscount" => $sale->details->taxaDiscount,
            "installment_tax_value" => FoxUtils::formatMoney($sale->installment_tax_value / 100),
            "taxaReal" => $sale->details->taxaReal,
        ];
    }

    public function headings(): array
    {
        return [
            "TRANSFERENCIA",
            "TIPO",
            "COMISSAO",
            "STATUS TRANSFERENCIA",
            "DATA TRANFERENCIA",
            "ANTECIPACAO",
            "PRODUTOR",
            // SALE
            "VENDA",
            "SUB-TOTAL",
            "TOTAL VENDA",
            "DATA VENDA",
            "MÉTODO",
            "STATUS VENDA",
            "CASHBACK",
            "DESCONTO AUTOMÁTICO",
            "DESCONTO",
            "TAXA (5.4%)",
            "TAXA PARCELAS",
            "TAXA TOTAL",
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = "A1:S1";
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
                $lastSale = null;
                for ($row = 2; $row <= $lastRow; $row++) {
                    $currentSale = $event->sheet
                        ->getDelegate()
                        ->getCellByColumnAndRow(1, $row)
                        ->getValue();
                    if ($currentSale != $lastSale && isset($lastSale)) {
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
                    $lastSale = $currentSale;
                }

                event(new SalesExportedEvent($this->user, $this->filename, $this->email));
            },
        ];
    }

    public function getReason()
    {
        if (!empty($this->transaction) && empty($this->reason)) {
            return "Transação";
        } elseif (!empty($this->transaction) && $this->reason == "chargedback") {
            return "Chargeback";
        } elseif ($this->reason == "refunded") {
            return "Estorno da transação";
        } elseif ($this->reason == "canceled_antifraud") {
            return "Chargeback";
        } else {
            return $this->reason;
        }
    }

    public function getAnticipatedCode()
    {
        $codeAnticipation = null;

        if (!empty($this->anticipation_id)) {
            $anticipation = $this->anticipation->first();
            $codeAnticipation = hashids_encode($anticipation->id, "anticipation_id");
        } elseif (!empty($this->transaction_id) && !empty($this->transaction->anticipatedTransactions()->first())) {
            $anticipatedTransaction = $this->transaction->anticipatedTransactions()->first();
            $codeAnticipation = hashids_encode($anticipatedTransaction->anticipation_id, "anticipation_id");
        }
        return $codeAnticipation;
    }

    private function getType($type_enum)
    {
        if ($type_enum == 1) {
            return "Entrada";
        }
        if ($type_enum == 2) {
            return "Saída";
        }
    }
}
