<?php

namespace Modules\Finances\Exports\Reports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Core\Events\ExtractExportedEvent;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\Transaction;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Services\FoxUtils;

class ExtractReportExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    use Exportable;

    private $filters;

    private $user;

    private $filename;

    public function __construct($filters, $user, $filename)
    {
        $this->filters = $filters;
        $this->user = $user;
        $this->filename = $filename;
    }

    public function query()
    {
        $transfersModel = new Transfer();
        $data = $this->filters;

        //parâmetros obrigatórios
        $companyId = current(Hashids::decode($data['company']));
        $dateRange = FoxUtils::validateDateRange($data["date_range"]);
        if ($data['date_type'] == 'transaction_date') {
            $dateType = 'transaction.created_at';
        } else if ($data['date_type'] == 'transfer_date') {
            $dateType = 'transfers.created_at';
        } else {
            $dateType = 'sales.start_date';
        }

        $transfers = $transfersModel->leftJoin('transactions as transaction', 'transaction.id', 'transfers.transaction_id')
            ->leftJoin('sales', 'sales.id', '=', 'transaction.sale_id')
            ->where(function ($query) use ($companyId) {
                $query->where('transfers.company_id', $companyId)
                    ->orWhere('transaction.company_id', $companyId);
            })
            ->whereBetween($dateType, [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);

        $saleId = str_replace('#', '', $data['transaction']);
        $saleId = current(Hashids::connection('sale_id')->decode($saleId));
        if ($saleId) {
            $transfers->where('transaction.sale_id', $saleId);
        }

        if(!empty($data['type'])){
            $transfers->where('transfers.type_enum', $transfersModel->present()->getTypeEnum($data['type']));
        }

        if(!empty($data['reason'])){
            $transfers->where('transfers.reason', 'like', '%' . $data['reason'] . '%');
        }

        if(!empty($data['value'])){
            $value = intval(preg_replace('/[^0-9]/', '', $data['value']));
            $transfers->where('transfers.value', $value);
        }

        $balanceInPeriod = $transfers->selectRaw("sum(CASE WHEN transfers.type_enum = 2 THEN (transfers.value * -1) ELSE transfers.value END) as balanceInPeriod")
            ->first();

        if(!empty($balanceInPeriod)){
            $balanceInPeriod = $balanceInPeriod->balanceInPeriod / 100;
            $balanceInPeriod = number_format($balanceInPeriod, 2, ',', '.');
        }

        $transfers = $transfers->select(
            'transfers.*',
            'transaction.sale_id',
            'transaction.company_id',
            'transaction.currency',
            'transaction.status',
            'transaction.type as transaction_type',
            'transaction.antecipable_value'
        )->orderBy('id', 'DESC');

        return $transfers;
    }

    public function map($row): array
    {
        $transfer = $row;

        $transferData = [];

        $transactionPresenter = (new Transaction())->present();

        if (!empty($transfer->transaction) && empty($transfer->reason)) {
            $reason = 'Transação';
        } else if (!empty($transfer->transaction) && $transfer->reason == 'chargedback') {
            $reason = 'Chargeback';
        } else if (empty($transfer->transaction) && ($transfer->reason ?? '') == 'chargedback') {
            $reason = 'Chargeback';
        } else if (!empty($transfer->transaction) && ($transfer->reason ?? '') == 'refunded') {
            $reason = 'Estorno da transação';
        } else {
            $reason = $transfer->reason ?? '';
        }

        $type     = $transfer->type_enum == 2 ? '-' : '';
        $value    = number_format(intval($type . $transfer->value) / 100, 2, ',', '.');
        // $currency = $transfer->currency == 'dolar' ? '$ ' . $value : 'R$ ';
        // $value    = $currency . $value;

        $transferData = [
            'reason' => $reason . '#'. Hashids::connection('sale_id')->encode($transfer->sale_id),
            'date'   => $transfer->created_at->format('d/m/Y'),
            'type'   => ($transfer->type_enum == 1) ? 'Entrada' : (($transfer->type_enum == 2) ? 'Saída' : ''),
            'value'  => $value,
        ];

        return $transferData;
    }

    public function headings(): array
    {
        return [
            'Razão',
            'Data da transferência',
            'Tipo',
            'Valor',
        ];
    }

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

                event(new ExtractExportedEvent($this->user, $this->filename));
            },
        ];
    }
}
