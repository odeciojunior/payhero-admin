<?php

namespace Modules\Sales\Exports\Reports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\SendgridService;

class BilletExpiredReportExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    use Exportable;

    private $filters;
    private $user;
    private $filename;
    private $email;

    public function __construct($filters, $user, $filename)
    {
        $this->filters = $filters;
        $this->user = $user;
        $this->filename = $filename;
        $this->email = !empty($filters["email"]) ? $filters["email"] : $user->email;
    }

    public function query()
    {
        $dateRange = foxutils()->validateDateRange($this->filters["date_range"]);
        $startDate = null;
        $endDate = null;

        if (!empty($dateRange) && $dateRange) {
            $startDate = $dateRange[0] . " 00:00:00";
            $endDate = $dateRange[1] . " 23:59:59";
        }

        $salesExpired = Sale::select(
            "sales.*",
            "checkout.email_sent_amount",
            "checkout.sms_sent_amount",
            "checkout.id as checkout_id",
            "checkout.id_log_session",
            DB::raw("(plan_sale.amount * plan_sale.plan_value ) AS value")
        )
            ->leftJoin("plans_sales as plan_sale", function ($join) {
                $join->on("plan_sale.sale_id", "=", "sales.id");
            })
            ->leftJoin("checkouts as checkout", function ($join) {
                $join->on("sales.checkout_id", "=", "checkout.id");
            })
            ->leftJoin("customers as customer", function ($join) {
                $join->on("sales.customer_id", "=", "customer.id");
            })
            ->whereIn("sales.status", [5])
            ->where([["sales.payment_method", Sale::BILLET_PAYMENT]])
            ->with([
                "project",
                "customer",
                "project.domains" => function ($query) {
                    $query
                        ->where("status", 3) //dominio aprovado
                        ->first();
                },
            ]);

        if (!empty($this->filters["client"])) {
            $customerSearch = Customer::where("name", "like", "%" . $this->filters["client"] . "%")
                ->pluck("id")
                ->toArray();
            $salesExpired->whereIn("sales.customer_id", $customerSearch);
        }

        $parseProjectIds = explode(",", $this->filters["project"]);
        $projectIds = [];
        if (!empty($parseProjectIds) && !in_array("all", $parseProjectIds)) {
            foreach ($parseProjectIds as $projectId) {
                array_push($projectIds, foxutils()->decodeHash($projectId));
            }
            $salesExpired->whereIn("sales.project_id", $projectIds);
        } else {
            // $userProjects = UserProject::select('project_id')
            //     ->where('user_id', $this->user->account_owner_id)
            //     ->where('type_enum', UserProject::TYPE_PRODUCER_ENUM)
            //     ->get()
            //     ->pluck('project_id')
            //     ->toArray();
            // $salesExpired->whereIn('sales.project_id', $userProjects);

            $salesExpired->where("sales.owner_id", $this->user->account_owner_id);
        }

        if (!empty($this->filters["client_document"])) {
            $customerSearch = Customer::where(
                "document",
                foxutils()->onlyNumbers($this->filters["client_document"])
            )->pluck("id");
            $salesExpired->whereIn("sales.customer_id", $customerSearch);
        }

        $parsePlanIds = explode(",", $this->filters["plan"]);
        $planIds = [];
        if (!empty($parsePlanIds) && !in_array("all", $parsePlanIds)) {
            foreach ($parsePlanIds as $planId) {
                array_push($planIds, hashids_decode($planId));
            }
            $salesExpired->whereHas("plansSales", function ($query) use ($planIds) {
                $query->whereIn("plan_id", $planIds);
            });
        }
        if (!empty($startDate) && !empty($endDate)) {
            $salesExpired->whereBetween("sales.created_at", [$startDate, $endDate]);
        } else {
            if (!empty($startDate)) {
                $salesExpired->whereDate("sales.created_at", ">=", $startDate);
            }
            if (!empty($dateEnd)) {
                $salesExpired->whereDate("sales.created_at", "<", $dateEnd);
            }
        }

        return $salesExpired;
    }

    public function map($row): array
    {
        $sale = $row;

        $sale->products = collect();

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
        $domainName = $domain->name ?? "cloudfox.net";
        $boletoLink =
            "https://checkout.{$domainName}/order/" . hashids_encode($sale->id, "sale_id") . "/download-boleto";

        $saleData = [];
        foreach ($sale->products as $product) {
            $productName = $product->name . ($product->description ? " (" . $product->description . ")" : "");

            $data = [
                //sale
                "sale_code" => "#" . hashids_encode($sale->id, "sale_id"),
                "shopify_order" => strval($sale->shopify_order),
                "payment_form" => $sale->present()->getPaymentForm(),
                "boleto_link" => $boletoLink ?? "",
                "boleto_digitable_line" => $sale->boleto_digitable_line ?? "",
                "start_date" => $sale->start_date . " " . $sale->hours,
                "boleto_due_date" => $sale->boleto_due_date,
                "total_paid" => $sale->total_paid_value ?? "",
                "subtotal" => $sale->sub_total,
                "shipping" => $sale->shipping->name ?? "",
                "shipping_value" => 'R$' . ($sale->shipment_value ?? 0),
                //plan
                "project_name" => $sale->project->name ?? "",
                "plan" => $product->plan_name,
                "price" => $product->plan_price,
                "product_id" => "#" . hashids_encode($product->id),
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
                "link" => $sale->checkout->present()->getCheckoutLink($sale->project->domains->first(), $this->user->company_default),
                "whatsapp_link" =>
                    "https://api.whatsapp.com/send?phone=+55" .
                    preg_replace("/[^0-9]/", "", $sale->customer->telephone) .
                    "&text=Olá " .
                    explode(" ", $sale->customer->name)[0],
            ];

            //remove caracteres indesejados em todos os campos
            $saleData[] = array_map(function ($item) {
                return preg_replace('/[^\w\s\p{P}\p{Latin}$]+/u', "", $item);
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
            "Link do Boleto",
            "Linha Digitavel do Boleto",
            "Data Inicial do Pagamento",
            "Data de Vencimento do Boleto",
            "Valor Total Venda",
            "Subtotal",
            "Frete",
            "Valor do Frete",
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
            "Src",
            "Utm_source",
            "Utm_medium",
            "Utm_campaign",
            "Utm_term",
            "Utm_content",
            "Utm_perfect",
            "Link",
            "Link Whatsapp",
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
                $sendGridService = new SendgridService();
                $userName = $this->user->name;
                $userEmail = $this->email;
                $downloadLink = getenv("APP_URL") . "/sales/download/" . $this->filename;

                $data = [
                    "name" => $userName,
                    "report_name" => "Relatório de Recuperação",
                    "download_link" => $downloadLink,
                ];

                $sendGridService->sendEmail(
                    "help@cloudfox.net",
                    "CloudFox",
                    $userEmail,
                    $userName,
                    "d-2279bf09c11a4bf59b951e063d274450",
                    $data
                );
            },
        ];
    }
}
