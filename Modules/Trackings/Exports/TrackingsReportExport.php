<?php

namespace Modules\Trackings\Exports;

use Illuminate\Support\Facades\DB;
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

        return $trackingService
            ->getTrackingsQueryBuilder($this->filters, $this->user->account_owner_id)
            ->join("customers as c", "c.id", "=", "s.customer_id")
            ->join("deliveries as d", "d.id", "=", "s.delivery_id")
            ->select([
                "products_plans_sales.id",
                "t2.id as tracking_id",
                "t2.tracking_code",
                "s.id as sale_id",
                "s.end_date as approved_date",
                DB::raw("ifnull(p.id, psa.id) as product_id"),
                DB::raw("ifnull(p.name, psa.name) as product_name"),
                "p.description as product_description",
                "p.sku as product_sku",
                "products_plans_sales.amount as product_amount",
                "c.name as customer_name",
                "c.document as customer_document",
                "d.street as delivery_street",
                "d.number as delivery_number",
                "d.complement as delivery_complement",
                "d.neighborhood as delivery_neighborhood",
                "d.zip_code as delivery_zip_code",
                "d.city as delivery_city",
                "d.state as delivery_state",
                "d.country as delivery_country",
            ]);
    }

    public function map($row): array
    {
        $productName = utf8_encode(
            $row->product_name . ($row->product_description ? " (" . $row->product_description . ")" : "")
        );
        $productID = hashids_encode($row->product_id);

        return [
            "sale" => "#" . hashids_encode($row->sale_id, "sale_id"),
            "tracking_code" => $row->tracking_code,
            "product_id" => "#" . $productID,
            "product_name" => $productName,
            "product_amount" => $row->product_amount,
            "product_sku" => $row->product_sku ?? "",
            "customer_name" => $row->customer_name ?? "",
            "customer_document" => $row->customer_document ?? "",
            "delivery_street" => $row->delivery_street ?? "",
            "delivery_number" => $row->delivery_number ?? "",
            "delivery_complement" => $row->delivery_complement ?? "",
            "delivery_neighborhood" => $row->delivery_neighborhood ?? "",
            "delivery_zip_code" => $row->delivery_zip_code ?? "",
            "delivery_city" => $row->delivery_city ?? "",
            "delivery_state" => $row->delivery_state ?? "",
            "delivery_country" => $row->delivery_country ?? "",
            "date" => !empty($row->approved_date) ? Carbon::parse($row->approved_date)->format("d/m/Y") : "",
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = "A1:S1"; // All headers
                $event->sheet
                    ->getDelegate()
                    ->getStyle($cellRange)
                    ->getFill()
                    ->setFillType("solid")
                    ->getStartColor()
                    ->setRGB("3e8ef7");
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
                    if ($currentSale != $lastSale) {
                        $setGray = !$setGray;
                    }
                    if ($setGray) {
                        $event->sheet
                            ->getDelegate()
                            ->getStyle("A" . $row . ":S" . $row)
                            ->getFill()
                            ->setFillType("solid")
                            ->getStartColor()
                            ->setRGB("e5e5e5");
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
            "Codigo da Venda",
            "Codigo de Rastreio",
            "Codigo do Produto",
            "Produto",
            "Quantidade",
            "SKU",
            "Nome do Cliente",
            "Documento",
            "Endereco",
            "Numero",
            "Complemento",
            "Bairro",
            "Cep",
            "Cidade",
            "Estado",
            "País",
            "Data Aprovacao",
        ];
    }
}
