<?php

namespace Modules\Sales\Transformers;

use App\Entities\Checkout;
use App\Entities\Client;
use App\Entities\Company;
use App\Entities\Plan;
use App\Entities\PlanSale;
use App\Entities\Project;
use App\Entities\Shipping;
use App\Entities\Transaction;
use App\Entities\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Vinkla\Hashids\Facades\Hashids;

class SaleApiResource extends Resource
{
    /**
     * @var User
     */
    private $user;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getUserModel()
    {
        if (!$this->user) {
            $this->user = app(User::class);
        }

        return $this->user;
    }

    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $userZmDeals = $this->getUserModel()->where('email', 'fernandomuniz1337@gmail.com')->first();

        $client = Client::find($this->client);

        $plansSale = PlanSale::where('sale', $this->id)->get()->toArray();
        $products  = [];
        $project   = '';
        foreach ($plansSale as $planSale) {
            $plano                  = Plan::with(['products'])->find($planSale['plan']);
            $cost                   = current($plano->products)[0]->cost ?? 0;
            $product                = [];
            $product['sku']         = $plano['id'];
            $product['name']        = $plano['name'];
            $product['amount']      = $planSale['amount'];
            $product['price']       = $planSale['plan_value'];
            $product['description'] = $plano['description'];
            $product['cost']        = $cost;
            $products[]             = $product;
        }

        if (count($plansSale) > 1) {
            $product = "Carrinho";
        }

        $userCompanies = Company::where('user_id', $userZmDeals->id)->pluck('id');

        $transaction = Transaction::where('sale', $this->id)->whereIn('company', $userCompanies)->first();

        if ($transaction) {
            $value = $transaction->value;
        } else {
            $value = '000';
        }

        //1 - aprovado
        //2 - pendente
        //3 - cancelado
        //4 - chargeback
        //10 - systemerror

        $checkout = Checkout::find($this->checkout);

        $statusValue = '';
        switch ($this->status) {
            case '1':
                $statusValue = 'aprovado';
                break;
            case '2':
                $statusValue = 'pendente';
                break;
            case '3':
                $statusValue = 'nao aprovado';
                break;
            case '4':
                $statusValue = 'estornado';
                break;
            default:
                $statusValue = 'canceled';
                break;
        }

        $shipping = Shipping::find($this->shipping);

        return [
            'sale_code'       => '#' . strtoupper(Hashids::connection('sale_id')->encode($this->id)),
            //'id'           => Hashids::connection('main')->encode($this->id),
            'id'              => $this->id,
            'project'         => $project,
            'products'        => $products,
            'client'          => $client['name'],
            'email'           => $client['email'],
            'doc'             => $client['document'],
            'payment_type'    => ($this->payment_method == 2) ? 'billet' : 'credit_card',
            'status'          => $statusValue,
            'data_compra'     => $this->start_date ? with(new Carbon($this->start_date))->format('d/m/Y H:i:s') : '',
            'end_date'        => $this->end_date ? with(new Carbon($this->end_date))->format('d/m/Y H:i:s') : '',
            'total_paid'      => ($this->dolar_quotation == '' ? 'R$ ' : 'US$ ') . substr_replace($value, '.', strlen($value) - 2, 0),
            'brand'           => $this->flag,
            'shipping'        => $shipping->value,
            'dolar_quotation' => $this->dolar_quotation,
            'src'             => $checkout->src,
            'utm_source'      => $checkout->utm_source,
            'utm_medium'      => $checkout->utm_medium,
            'utm_campaign'    => $checkout->utm_campaign,
            'utm_term'        => $checkout->utm_term,
            'utm_content'     => $checkout->utm_content,
        ];
    }
}
