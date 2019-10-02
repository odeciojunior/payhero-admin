<?php

namespace Modules\Sales\Transformers;

use Carbon\Carbon;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Transaction;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class SalesResource extends Resource
{
    public function toArray($request)
    {
        $companyModel = new Company();
        $transactionModel = new Transaction();

        $this['hours'] = (new Carbon($this['start_date']))->format('H:m:s');
        $this['start_date'] = (new Carbon($this['start_date']))->format('d/m/Y');

        if (isset($this['boleto_due_date'])) {
            $this['boleto_due_date'] = (new Carbon($this['boleto_due_date']))->format('d/m/Y');
        }

        if ($this->flag) {
            $this['flag'] = $this->flag;
        } else if ((!$this->flag || empty($this->flag)) && $this->payment_method == 1) {
            $this['flag'] = 'generico';
        } else {
            $this['flag'] = 'boleto';
        }

        $discount = '0,00';
        $subTotal = $this->present()->getSubTotal();
        $total = $subTotal;

        $total += preg_replace("/[^0-9]/", "", $this->shipment_value);
        if (preg_replace("/[^0-9]/", "", $this->shopify_discount) > 0) {
            $total -= preg_replace("/[^0-9]/", "", $this->shopify_discount);
            $discount = preg_replace("/[^0-9]/", "", $this->shopify_discount);
        } else {
            $discount = '0,00';
        }

        $this->shipment_value = preg_replace('/[^0-9]/', '', $this->shipment_value);

        $userCompanies = $companyModel->where('user_id', auth()->user()->id)->pluck('id');
        $transaction = $transactionModel->where('sale_id', $this->id)->whereIn('company_id', $userCompanies)
            ->first();

        $transactionConvertax = $transactionModel->where('sale_id', $this->id)
            ->where('company_id', 29)
            ->first();

        if (!empty($transactionConvertax)) {
            $convertaxValue = ($transaction->currency == 'real' ? 'R$ ' : 'US$ ') . substr_replace($transactionConvertax->value, ',', strlen($transactionConvertax->value) - 2, 0);
        } else {
            $convertaxValue = '0,00';
        }

        $value = $transaction->value;

        $comission = ($transaction->currency == 'real' ? 'R$ ' : 'US$ ') . substr_replace($value, ',', strlen($value) - 2, 0);

        $taxa = 0;
        $taxaReal = 0;

        if ($this->dolar_quotation != 0) {
            $taxa = intval($total / $this->dolar_quotation);
            $taxaReal = 'US$ ' . number_format((intval($taxa - $value)) / 100, 2, ',', '.');
            $total += preg_replace('/[^0-9]/', '', $this->iof);
        } else {
            $taxaReal = ($total / 100) * $transaction->percentage_rate + 100;
            $taxaReal = 'R$ ' . number_format($taxaReal / 100, 2, ',', '.');
        }

        return [
            //hide ids
            'id' => Hashids::connection('sale_id')->encode($this->id),
            'delivery_id' => Hashids::encode($this['delivery_id']),
            'checkout_id' => Hashids::encode($this['checkout_id']),
            'client_id' => Hashids::encode($this['client_id']),
            //sale
            'payment_method' => $this->payment_method,
            'flag' => $this->flag,
            'start_date' => $this->start_date,
            'hours' => $this->hours,
            'status' => $this->status,
            'dolar_quotation' => $this->dolar_quotation,
            'iof' => $this->iof,
            'installments_amount' => $this->installments_amount,
            'boleto_link' => $this->boleto_link,
            'boleto_digitable_line' => $this->boleto_digitable_line,
            'boleto_due_date' => $this->boleto_due_date,
            'attempts' => $this->attempts,
            //transaction
            'transaction_rate' => $transaction->transaction_rate,
            'percentage_rate' => $transaction->percentage_rate,
            //extra info
            'total' => number_format(intval($total) / 100, 2, ',', '.'),
            'subTotal' => number_format(intval($subTotal) / 100, 2, ',', '.'),
            'discount' => number_format(intval($discount) / 100, 2, ',', '.'),
            'shipment_value' => number_format(intval($this->shipment_value) / 100, 2, ',', '.'),
            'comission' => $comission,
            'convertax_value' => $convertaxValue,
            'taxa' => number_format($taxa / 100, 2, ',', '.'),
            'taxaReal' => $taxaReal,
        ];
    }
}
