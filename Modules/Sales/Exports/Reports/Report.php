<?php

namespace Modules\Sales\Exports\Reports;

use Carbon\Carbon;
use Modules\Core\Entities\User;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\SaleService;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\Core\Events\SalesExportedEvent;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Report implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    use Exportable;

    private $filters;

    private $user;

    private $filename;

    public function __construct($filters = null, $user = null, $filename = 'arquivo.xls'){
        $this->filters  = $filters;
        $this->user     = User::find(24);
        $this->filename = $filename;
    }

    public function query()
    {

        // SELECT count(*) as t_count, sale_id FROM `transactions` WHERE status in ('paid', 'transfered') group BY sale_id ORDER BY t_count DESC limit 250

        $data = Transaction::selectRaw('sale_id, transactions.id, fantasy_name, value, transactions.created_at')
                            ->join('companies', 'company_id', 'companies.id')
                            ->whereRaw("sale_id IN ('184340','168175','173651','182919','235479','50397','127525','89524','165410','96095','209409','155379','211457','166421', '38161','168686','172741','132421','179943','110939','154692','159452','106670','86760','197990','67294','239153','180017', '176561','181595','105822','160265','114464','60007','76954','154251','159343','129864','180227','156828','27861','117638')")
                            ->whereNull('invitation_id')
                            ->whereNotNull('company_id')
                            ->orderBy('sale_id');

        return $data;
    }

    public function map($row): array
    {
        return [
            'sale_id'      => $row->sale_id,
            'id'           => '#' . Hashids::connection('sale_id')->encode($row->sale_id),
            'fantasy_name' => $row->fantasy_name,
            'value'        => number_format(intval($row->value) / 100, 2, ',', '.'),
            'created_at'   => $row->created_at
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            '#id',
            'empresa',
            'valor',
            'data',
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

                event(new SalesExportedEvent($this->user, $this->filename));
            },
        ];
    }
}


