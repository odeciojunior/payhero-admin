<?php

namespace Modules\Sales\Exports\Reports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Core\Entities\Domain;
use Modules\Core\Events\SalesExportedEvent;
use Modules\Core\Services\SaleService;
use Vinkla\Hashids\Facades\Hashids;

class SaleReportExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    use Exportable;

    private $filters;

    private $user;

    private $filename;

    private $saleService;

    private $email;

    public function __construct($filters, $user, $filename)
    {
        $this->filters = $filters;
        $this->saleService = new SaleService();
        $this->user = $user;
        $this->filename = $filename;
        $this->email = !empty($filters["email"]) ? $filters["email"] : $user->email;
    }

    public function query()
    {
        return $this->saleService->getSalesQueryBuilder($this->filters, true, $this->user->account_owner_id);
    }

    public function map($row): array
    {
        $userCompany = $row->only("company_id");

        $sale = $row->sale;

        $sale->products = collect();
        $this->saleService->getDetails($sale, $userCompany);
        foreach ($sale->productsPlansSale as &$pps) {
            $plan = $pps->plan;
            $pps->product["amount"] = $pps->amount;
            $pps->product["plan_name"] = $plan->name;
            $pps->product["plan_price"] = $plan->price;
            $sale->products->add($pps->product);
        }

        $domain = Domain::select("name")
            ->where("project_id", $sale->project_id)
            ->where("status", 3)
            ->first();
        $domainName = $domain->name ?? "nexuspay.vip";
        $boletoLink =
            "https://checkout.{$domainName}/order/" .
            Hashids::connection("sale_id")->encode($sale->id) .
            "/download-boleto";

        $saleData = [];
        foreach ($sale->products as $key => $product) {
            $firstLine = $key === 0;

            $productName = $product->name . ($product->description ? " (" . $product->description . ")" : "");

            $data = [
                //sale
                "sale_code" => "#" . Hashids::connection("sale_id")->encode($sale->id),
                "shopify_order" => strval($sale->shopify_order),
                "payment_form" => $sale->present()->getPaymentForm(),
                "installments_amount" => $sale->installments_amount ?? "",
                "flag" => $sale->flag ?? "",
                "boleto_link" => $boletoLink ?? "",
                "boleto_digitable_line" => $sale->boleto_digitable_line ?? "",
                "boleto_due_date" => $sale->boleto_due_date,
                "start_date" => $sale->start_date . " " . $sale->hours,
                "end_date" => $sale->end_date ? Carbon::parse($sale->end_date)->format("d/m/Y H:i:s") : "",
                "status" => $sale->present()->getStatus(),
                "total_paid" => $firstLine ? $sale->total_paid_value : "",
                "subtotal" => $firstLine ? $sale->sub_total : "",
                "shipping" => $sale->shipping->name ?? "",
                "shipping_value" => $firstLine ? $sale->shipment_value : "",
                "fee" => $firstLine ? foxutils()->stringToMoney($sale->details->taxaReal) : "",
                "comission" => $firstLine ? foxutils()->stringToMoney($sale->details->comission) : "",
                //plan
                "project_name" => $sale->project->name ?? "",
                "plan" => $product->plan_name,
                "price" => $product->plan_price,
                "product_id" => "#" . Hashids::encode($product->id),
                "product" => $productName,
                "product_shopify_id" => $product->shopify_id,
                "product_shopify_variant_id" => $product->shopify_variant_id,
                "amount" => $product->amount,
                "sku" => $product->sku,
                //client
                "client_name" => $sale->customer->name ?? "",
                "client_telephone" => $sale->customer->telephone ?? "",
                "client_email" => $sale->customer->email ?? "",
                "client_document" => $sale->customer->document ?? "",
                "client_street" => $sale->delivery->street ?? "",
                "client_number" => $sale->delivery->number ?? "",
                "client_complement" => $sale->delivery->complement ?? "",
                "client_neighborhood" => $sale->delivery->neighborhood ?? "",
                "client_zip_code" => $sale->delivery->zip_code ?? "",
                "client_city" => $sale->delivery->city ?? "",
                "client_state" => $sale->delivery->state ?? "",
                "client_country" => $sale->delivery->country ?? "",
                //track
                "src" => $sale->checkout->src ?? "",
                "utm_source" => $sale->checkout->utm_source ?? "",
                "utm_medium" => $sale->checkout->utm_medium ?? "",
                "utm_campaign" => $sale->checkout->utm_campaign ?? "",
                "utm_term" => $sale->checkout->utm_term ?? "",
                "utm_content" => $sale->checkout->utm_content ?? "",
            ];

            //remove caracteres indesejados em todos os campos
            $saleData[] = array_map(function ($item) {
                return preg_replace("/[^\p{Latin}[:alnum:][:punct:]\s_-]/u", "", $item);
            }, $data);
        }

        return $saleData;
    }

    public function headings(): array
    {
        return [
            //sale
            "Código da Venda",
            "Pedido do Shopify",
            "Forma de Pagamento",
            "Número de Parcelas",
            "Bandeira do Cartão",
            "Link do Boleto",
            "Linha Digitavel do Boleto",
            "Data de Vencimento do Boleto",
            "Data Inicial do Pagamento",
            "Data Final do Pagamento",
            "Status",
            "Valor Total Venda",
            "Subtotal",
            "Frete",
            "Valor do Frete",
            "Taxas",
            "Comissão",
            //plan
            "Projeto",
            "Plano",
            "Preço do Plano",
            "Código dos produtos",
            "Produto",
            "Id do Shopify",
            "Id da Variante do Shopify",
            "Quantidade dos Produtos",
            "SKU",
            //client
            "Nome do Cliente",
            "Telefone do Cliente",
            "Email do Cliente",
            "Documento",
            "Endereço",
            "Número",
            "Complemento",
            "Bairro",
            "Cep",
            "Cidade",
            "Estado",
            "País",
            //track
            "src",
            "utm_source",
            "utm_medium",
            "utm_campaign",
            "utm_term",
            "utm_content",
            "utm_perfect",
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = "A1:AS1"; // All headers
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
}
