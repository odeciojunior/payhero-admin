<?php

namespace Modules\Affiliates\Http\Controllers;

use App\Entities\Plan;
use App\Entities\User;
use App\Entities\Pixel;
use App\Entities\Domain;
use App\Entities\Project;
use App\Entities\Campaign;
use App\Entities\PlanSale;
use App\Entities\Affiliate;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use App\Entities\AffiliateLink;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;

class CampaignsController extends Controller {


    public function index(Request $request){

        $dados = $request->all();

        $campaigns = \DB::table('campaigns as campaign')
        ->get([
            'id',
            'description',
            'affiliate',
        ])
        ->where('affiliate',Hashids::decode($dados['affiliate'])[0]);

        return Datatables::of($campaigns)
        ->addColumn('clicks_amount', function ($campaign) {
            $affiliateLinks = AffiliateLink::where('campaign',$campaign->id)->get()->toArray();
            if(count($affiliateLinks) < 1){
                return "0";
            }
            $clicksAmount = 0;
            foreach($affiliateLinks as $link_affiliate){
                $clicksAmount += $link_affiliate['clicks_amount'];
            }
            return $clicksAmount;
        })
        ->addColumn('detalhes', function ($campaign) {
            return "<span data-toggle='modal' data-target='#modal_dados_campaign'>
                        <a class='btn btn-outline btn-success dados_campaign' data-placement='top' data-toggle='tooltip' title='Dados da campaign' campaign='".Hashids::encode($campaign->id)."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                            Dados da campaign
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);

    }

    public function details(Request $request){

        $dados = $request->all();

        $campaign = Campaign::where('id',Hashids::decode($dados['campaign']))->first();;

        $affiliate = Affiliate::find($campaign->affiliate);

        $project = Project::find($affiliate['project']);

        $domain = Domain::where('project',$affiliate['project'])->first();

        $setCookieUrl = "affiliate.".$domain['name']."/"."setcookie/";

        $urlPage = $setCookieUrl.AffiliateLink::where([
            ['affiliate', $affiliate['id']],
            ['plan' , null]
        ])->first()['parameter'];

        $plans = Plan::where('project',$project['id'])->get()->toArray();

        foreach($plans as &$plan){
            $plan['profit'] = number_format($plan['price'] * $project['percentage_affiliates'] / 100, 2);
            $plan['url'] = $setCookieUrl.AffiliateLink::where([
                ['affiliate', $affiliate['id']],
                ['plan' , $plan['id']]
            ])->first()['parameter'];
        }

        $pixels = Pixel::where('campaign',$campaign->id)->get()->toArray();

        $campaignData = view('affiliates::campaign',[
            'plans'       => $plans,
            'url_pagina'  => $urlPage,
            'project'     => $project,
            'pixels'      => $pixels,
            'id_campaign' => $campaign->id
        ]);

        return response()->json($campaignData->render());

    }

    public function store(Request $request){

        $dados = $request->all();

        $affiliate = Affiliate::where('id',Hashids::decode($dados['affiliate']))->first();

        $project = Project::find($affiliate->projeto);

        $plans = Plan::where('project',$project['id'])->get()->toArray();

        $dados['affiliate'] = $affiliate->id;

        $campaign = Campaign::create($dados);

        AffiliateLink::create([
            'affiliate' => $affiliate->id,
            'parameter' => $this->randString(12),
            'campaign'  => $campaign->id
        ]);

        if($project['url_cookies_checkout']){
            foreach($plans as $plan){
                AffiliateLink::create([
                    'affiliate' => $affiliate->id,
                    'parameter' => $this->randString(12),
                    'plan'      => $plan['id'],
                    'campaign'  => $campaign->id
                ]);
            }
        }

        return response()->json('sucesso');
    }

    public function sales(Request $request){

        $dados = $request->all();

        $sales = \DB::table('sales')
            ->leftjoin('clients as client', 'client.id', '=', 'sales.client')
            ->where('sales.affiliate',Hashids::decode($dados['affiliate']))
            ->get([
                'sales.id',
                'client.name as client',
                'sales.payment_form as payment_form',
                'sales.gateway_status as status',
                'sales.start_date as data',
                'sales.end_date as end_date',
                'sales.total_value_paid as valor_total',
                'sales.shipment_value',
                'sales.affiliate',
        ]);

        return Datatables::of($sales)
        ->addColumn('description', function ($sale) {
            $planSales = PlanSale::where('sale',$sale->id)->get()->toArray();
            if(count($planSales) > 1){
                return "Carrinho";
            }
            foreach($planSales as $planSale){
                $plan = Plan::find($planSale['plan']);
                return substr($plan['name'],0,25);
            }
        })
        ->addColumn('valor_liquido', function ($sale) {
            $shipmentValue = str_replace('.','',$sale->shipment_value);
            if($shipmentValue == ''){
                return $sale->valor_total;
            }
            $liquidValue = str_replace('.','',$sale->valor_total) - $shipmentValue;
            return substr_replace($liquidValue, '.',strlen($liquidValue) - 2, 0 );
        })
        ->editColumn('data', function ($sale) {
            return $sale->data ? with(new Carbon($sale->data))->format('d/m/Y H:i:s') : '';
        })
        ->editColumn('end_date', function ($sale) {
            return $sale->end_date ? with(new Carbon($sale->end_date))->format('d/m/Y H:i:s') : '';
        })
        ->editColumn('payment_form', function ($sale) {
            if($sale->payment_form == 'Cartão de crédito') 
                return 'Cartão';
            if($sale->payment_form == 'boleto') 
                return 'Boleto';
            return $sale->payment_form;
        })
        ->editColumn('status', function ($sale) {
            if($sale->status == 'paid')
                return "<span class='badge badge-round badge-success'>Aprovada</span>";
            if($sale->status == 'refused')
                return "<span class='badge badge-round badge-danger'>Rejeitada</span>";
            if($sale->status == 'waiting_payment')
                return "<span class='badge badge-round badge-info'>Aguardando pagamento</span>";
            if($sale->status == 'refunded')
                return "<span class='badge badge-round badge-default'>Estornada</span>";
            if($sale->status == '')
                return "<span class='badge-round badge-info'>- - - -</span>";
            return $sale->status;
        })
        ->addColumn('detalhes', function ($sale) {
            $buttons = "<button class='btn btn-sm btn-outline btn-primary detalhes_sale' sale='".$sale->id."' data-target='#modal_detalhes' data-toggle='modal' type='button'>
                            Detalhes
                        </button>";
            return $buttons;
        })
        ->rawColumns(['detalhes','status'])
        ->make(true);

    }

    function randString($size){

        $newParameter = false;

        while(!$newParameter){

            $basic = 'abcdefghijlmnopqrstuvwxyz0123456789';

            $parameter = '';

            for($count= 0; $size > $count; $count++){
                $parameter.= $basic[rand(0, strlen($basic) - 1)];
            }

            $newLink = AffiliateLink::where('parameter', $parameter)->first();

            if($newLink == null){
                $newParameter = true;
            }

        }

        return $parameter;
    }

}
