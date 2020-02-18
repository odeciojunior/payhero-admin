<?php

namespace Modules\Trackings\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Core\Events\TrackingsExportedEvent;
use Modules\Core\Services\TrackingService;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;

class TrackingsReportExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    use Exportable;

    private $filters;

    private $filename;

    private $user;

    public function __construct($filters, $user, $filename)
    {
        $this->filters = $filters;
        $this->user = $user;
        $this->filename = $filename;
    }

    public function query()
    {
        $trackingService = new TrackingService();

        return $trackingService->getTrackingsQueryBuilder($this->filters, $this->user->account_owner_id);
    }

    public function map($row): array
    {
        $return = [
            'sale' => '#' . Hashids::connection('sale_id')->encode($row->sale->id),
            'tracking_code' => '',
            'product_id' => '#' . Hashids::encode($row->product->id),
            'product_name' => $row->product->name . ($row->product->description ? ' (' . $row->product->description . ')' : ''),
            'product_amount' => $row->amount,
            'product_sku' => $row->product->sku,
            'client_name' => $row->sale->customer->name ?? '',
            'client_telephone' => $row->sale->customer->telephone ?? '',
            'client_email' => $row->sale->customer->email ?? '',
            'client_document' => $row->sale->customer->document ?? '',
            'client_street' => $row->sale->delivery->street ?? '',
            'client_number' => $row->sale->delivery->number ?? '',
            'client_complement' => $row->sale->delivery->complement ?? '',
            'client_neighborhood' => $row->sale->delivery->neighborhood ?? '',
            'client_zip_code' => $row->sale->delivery->zip_code ?? '',
            'client_city' => $row->sale->delivery->city ?? '',
            'client_state' => $row->sale->delivery->state ?? '',
            'client_country' => $row->sale->delivery->country ?? '',
            'date' => (!empty($row->sale->end_date)) ? Carbon::parse($row->sale->end_date)->format('d/m/Y') : '',
        ];

        if ($row->tracking) {
            $return['tracking_code'] = $row->tracking->tracking_code;
        }

        return $return;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
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
                    $currentSale = $event->sheet->getDelegate()->getCellByColumnAndRow(1, $row)->getValue();
                    if ($currentSale != $lastSale) {
                        $setGray = !$setGray;
                    }
                    if ($setGray) {
                        $event->sheet->getDelegate()
                            ->getStyle('A' . $row . ':R' . $row)
                            ->getFill()
                            ->setFillType('solid')
                            ->getStartColor()
                            ->setRGB('e5e5e5');
                    }
                    $lastSale = $currentSale;
                }
                event(new TrackingsExportedEvent($this->user, $this->filename));
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
            'Código de Rastreio',
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
            'Data Aprovação',
        ];
    }
}
