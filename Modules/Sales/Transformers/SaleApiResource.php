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
        $userZmDeals = $this->getUserModel()->where('email', 'zmdeals@mail.com')->first();

        $client = Client::find($this->client);

        $plansSale = PlanSale::where('sale', $this->id)->get()->toArray();
        $product   = '';
        $project   = '';
        foreach ($plansSale as $planSale) {
            $plano   = Plan::find($planSale['plan']);
            $product = $plano['name'];
            $project = Project::find($plano['project']);
            $project = $project['name'];
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
                $statusValue = 'completed';
                break;
            case '2':
                $statusValue = 'canceled';
                break;
            case '3':
                $statusValue = 'canceled';
                break;
            case '4':
                $statusValue = 'canceled';
                break;
            default:
                $statusValue = 'canceled';
                break;
        }

        $shipping = Shipping::find($this->shipping);

        return [
            'sale_code'        => '#' . strtoupper(Hashids::connection('sale_id')->encode($this->id)),
            //'id'           => Hashids::connection('main')->encode($this->id),
            'id'               => $this->id,
            'project'          => $project,
            'product'          => $product,
            'client'           => $client['name'],
            'email'            => $client['email'],
            'doc'              => $client['document'],
            'payment_type'     => ($this->payment_method == 2) ? 'billet' : 'credit_card',
            'status'           => $statusValue,
            'data_compra'      => $this->start_date ? with(new Carbon($this->start_date))->format('d/m/Y H:i:s') : '',
            'end_date'         => $this->end_date ? with(new Carbon($this->end_date))->format('d/m/Y H:i:s') : '',
            'total_paid'       => ($this->dolar_quotation == '' ? 'R$ ' : 'US$ ') . substr_replace($value, '.', strlen($value) - 2, 0),
            'brand'            => $this->flag,
            'dollar_quotation' => $this->dolar_quotation,
            'shipping'         => $shipping->value,
            'src'              => $checkout->src,
            'utm_source'       => $checkout->utm_source,
            'utm_medium'       => $checkout->utm_medium,
            'utm_campaign'     => $checkout->utm_campaign,
            'utm_term'         => $checkout->utm_term,
            'utm_content'      => $checkout->utm_content,
        ];
    }
}
