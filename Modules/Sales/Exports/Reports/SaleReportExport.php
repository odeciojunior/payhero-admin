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

    public function __construct($filters, $user, $filename)
    {
        $this->filters = $filters;
        $this->saleService = new SaleService();
        $this->user = $user;
        $this->filename = $filename;
    }

    public function query()
    {
        return $this->saleService->getSalesQueryBuilder($this->filters, true, $this->user->id);
    }

    public function map($row): array
    {
        $userCompany = $row->only('company_id');

        $sale = $row->sale;

        $sale->products = collect();
        $this->saleService->getDetails($sale, $userCompany);
        foreach ($sale->plansSales as &$planSale) {
            $plan = $planSale->plan;
            if(isset($plan)) {
                foreach ($plan->productsPlans as $productPlan) {
                    $productPlan->product['amount'] = $productPlan->amount * $planSale->amount;
                    $productPlan->product['plan_name'] = $plan->name;
                    $productPlan->product['plan_price'] = $plan->price;
                    $sale->products->add($productPlan->product);
                }
            }
        }

        $saleData = [];
        foreach ($sale->products as $product) {
            $saleData[] = [
                //sale
                'sale_code' => '#' . Hashids::connection('sale_id')->encode($sale->id),
                'shopify_order' => strval($sale->shopify_order),
                'payment_form' => $sale->payment_method == 2 ? 'Boleto' : (($sale->payment_method == 1 || $sale->payment_method == 3) ? 'Cartão' : ''),
                'installments_amount' => $sale->installments_amount ?? '',
                'flag' => $sale->flag ?? '',
                'boleto_link' => $sale->boleto_link ?? '',
                'boleto_digitable_line' => $sale->boleto_digitable_line ?? '',
                'boleto_due_date' => $sale->boleto_due_date,
                'start_date' => $sale->start_date . ' ' . $sale->hours,
                'end_date' => $sale->end_date ? Carbon::parse($sale->end_date)
                    ->format('d/m/Y H:i:s') : '',
                'status' => $sale->present()->getStatus(),
                'total_paid' => $sale->total_paid_value ?? '',
                'shipping' => $sale->shipping->name ?? '',
                'shipping_value' => $sale->shipping->value ?? '',
                'fee' => $sale->details->taxaReal,
                'comission' => $sale->details->comission,
                //plan
                'project_name' => $sale->project->name ?? '',
                'plan' => $product->plan_name,
                'price' => $product->plan_price,
                'product_id' => '#' . Hashids::encode($product->id),
                'product' => $product->name . ($product->description ? ' (' . $product->description . ')' : ''),
                'product_shopify_id' => $product->shopify_id,
                'product_shopify_variant_id' => $product->shopify_variant_id,
                'amount' => $product->amount,
                'sku' => $product->sku,
                //client
                'client_name' => $sale->client->name ?? '',
                'client_telephone' => $sale->client->telephone ?? '',
                'client_email' => $sale->client->email ?? '',
                'client_document' => $sale->client->document ?? '',
                'client_street' => $sale->delivery->street ?? '',
                'client_number' => $sale->delivery->number ?? '',
                'client_complement' => $sale->delivery->complement ?? '',
                'client_neighborhood' => $sale->delivery->neighborhood ?? '',
                'client_zip_code' => $sale->delivery->zip_code ?? '',
                'client_city' => $sale->delivery->city ?? '',
                'client_state' => $sale->delivery->state ?? '',
                'client_country' => $sale->delivery->country ?? '',
                //track
                'src' => $sale->checkout->src ?? '',
                'utm_source' => $sale->checkout->utm_source ?? '',
                'utm_medium' => $sale->checkout->utm_medium ?? '',
                'utm_campaign' => $sale->checkout->utm_campaign ?? '',
                'utm_term' => $sale->checkout->utm_term ?? '',
                'utm_content' =>  $sale->checkout ? preg_replace('/[^\p{Latin}[:punct:]\d\s+]/u', '', $sale->checkout->utm_content) : '',
            ];
        }

        //foreach ($saleData as &$value){
        //    $value = preg_replace('/[^\p{Latin}[:punct:]\d\s+]/u', '!Te peguei!', $value);
        //}

        return $saleData;
    }

    public function headings(): array
    {
        return [
            //sale
            'Código da Venda',
            'Pedido do Shopify',
            'Forma de Pagamento',
            'Número de Parcelas',
            'Bandeira do Cartão',
            'Link do Boleto',
            'Linha Digitavel do Boleto',
            'Data de Vencimento do Boleto',
            'Data Inicial do Pagamento',
            'Data Final do Pagamento',
            'Status',
            'Valor Total Venda',
            'Frete',
            'Valor do Frete',
            'Taxas',
            'Comissão',
            //plan
            'Projeto',
            'Plano',
            'Preço do Plano',
            'Código dos produtos',
            'Produto',
            'Id do Shopify',
            'Id da Variante do Shopify',
            'Quantidade dos Produtos',
            'SKU',
            //client
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
            //track
            'src',
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_term',
            'utm_content',
            'utm_perfect',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $cellRange = 'A1:AR1'; // All headers
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
                            ->getStyle('A' . $row . ':AR' . $row)
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
