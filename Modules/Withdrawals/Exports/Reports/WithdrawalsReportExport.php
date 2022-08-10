<?php

namespace Modules\Withdrawals\Exports\Reports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Events\FinancesExportedEvent;
use Modules\Core\Events\WithdrawalsExportedEvent;
use Modules\Transfers\Services\GetNetStatementService;
use Modules\Withdrawals\Transformers\WithdrawalTransactionsResource;

//, WithMapping, WithHeadings, ShouldAutoSize, WithEvents
class WithdrawalsReportExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithEvents
{
    use Exportable;

    protected $items;

    protected $user;

    protected $filename;

    protected $email;

    protected $arrayTotal;

    protected $withdrawalId;

    public function __construct($withdrawalId, $user, $email, $filename)
    {
        $transactions = Transaction::with("sale")
            ->with("company")
            ->where("withdrawal_id", $withdrawalId);
        $items = WithdrawalTransactionsResource::collection($transactions->orderBy("id", "ASC")->get());
        $this->items = collect($items);
        $this->user = $user;
        $this->filename = $filename;
        $this->email = $email;
    }

    public function collection()
    {
        return $this->items;
    }

    public function map($row): array
    {
        return [
            $row["transaction_code"],
            $row["brand"],
            $row["liquidated"] ? "Sim" : "Não",
            $row["date"],
            $row["value"],
        ];
    }
    ////
    public function headings(): array
    {
        return ["Código Transação", "Forma", "Transferido", "Data de transferência", "Valor"];
    }

    //
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = "A1:E1"; // All headers
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

                event(new WithdrawalsExportedEvent($this->user, $this->filename, $this->email));
            },
        ];
    }
}
