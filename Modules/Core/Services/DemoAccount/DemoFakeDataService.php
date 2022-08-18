<?php

namespace Modules\Core\Services\DemoAccount;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\UnicodropIntegration;
use Modules\Core\Entities\Whatsapp2Integration;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Entities\HotbilletIntegration;
use Modules\Core\Entities\HotzappIntegration;
use Modules\Core\Entities\MelhorenvioIntegration;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\ReportanaIntegration;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\SmartfunnelIntegration;
use Modules\Core\Entities\ConvertaxIntegration;
use Modules\Core\Entities\ActivecampaignIntegration;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\ApiToken;
use Modules\Core\Entities\AstronMembersIntegration;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\BlockReason;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleContestation;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\User;
use Modules\Core\Services\SaleService;

class DemoFakeDataService
{
    public function createFakeContestation(){

        $sales = Sale::select('sales.id')
                ->leftJoin('sale_contestations as c','sales.id','=','c.sale_id')
                ->whereNull('c.id')
                ->where('sales.gateway_id',Gateway::SAFE2PAY_PRODUCTION_ID)
                ->where('sales.status',Sale::STATUS_APPROVED)
                ->inRandomOrder()
                ->limit(1)
                ->get();        

        $blockStatus = BlockReasonSale::STATUS_BLOCKED;
        $blockObs = 'Em disputa';
        foreach($sales as $sale){
            $contestation = SaleContestation::factory(1)->for($sale)->create()->first();
            switch ($contestation->status) {
                case SaleContestation::STATUS_IN_PROGRESS:
                    $blockStatus = BlockReasonSale::STATUS_BLOCKED;
                    $blockObs = 'Em disputa';
                break;                
                case SaleContestation::STATUS_WIN:
                    $blockStatus = BlockReasonSale::STATUS_UNLOCKED;
                    $blockObs = 'Em disputa';
                break;                
            }

            BlockReasonSale::create([
                'sale_id'=>$sale->id,
                'blocked_reason_id'=>BlockReason::IN_DISPUTE,
                'status'=>$blockStatus,    
                'observation'=>$blockObs
            ]);
        }
    }

    public function createChargeback()
    {
        $contestationCount = DB::table('sale_contestations')->where('status','<>',SaleContestation::STATUS_LOST)->count();
        if($contestationCount % 20 == 0)
        {
            $saleContestation = SaleContestation::where('status','=',SaleContestation::STATUS_IN_PROGRESS)->orderBy('id','DESC')->first();
           
            $sale = Sale::find($saleContestation->sale_id);

            $refundTransactions = $sale->transactions;
        
            $saleService = new SaleService();
            
            $cashbackValue = !empty($sale->cashback->value) ? $sale->cashback->value:0;
            $saleTax = $saleService->getSaleTaxRefund($sale,$cashbackValue);
            
            $safe2payBalance = 0;

            foreach ($refundTransactions as $refundTransaction) 
            {            
                $company = $refundTransaction->company;
                if (!empty($company))
                {
                    $safe2payBalance = $company->safe2pay_balance;

                    $refundValue = $refundTransaction->value;
                    if ($refundTransaction->type == Transaction::TYPE_PRODUCER) {
                        $refundValue += $saleTax;
                    }
    
                    if ($refundTransaction->status_enum <> Transaction::STATUS_TRANSFERRED)
                    {                        
                        $safe2payBalance += $refundTransaction->value;
                        Transfer::create(
                            [
                                'transaction_id' => $refundTransaction->id,
                                'user_id' => $company->user_id,
                                'company_id' => $company->id,
                                'type_enum' => Transfer::TYPE_IN,
                                'value' => $refundTransaction->value,
                                'type' => 'in',
                                'gateway_id' => Gateway::SAFE2PAY_PRODUCTION_ID
                            ]
                        );
    
                        $company->update([
                            'safe2pay_balance' => $safe2payBalance
                        ]);
                    } 
                        
                    Transfer::create([
                        'transaction_id' => $refundTransaction->id,
                        'user_id' => $refundTransaction->user_id,
                        'company_id' => $refundTransaction->company_id,
                        'gateway_id' => Gateway::SAFE2PAY_PRODUCTION_ID,
                        'value' => $refundValue,
                        'type' => 'out',
                        'type_enum' => Transfer::TYPE_OUT,
                        'reason' => 'chargedback',
                        'is_refunded_tax' => 0
                    ]);
            
                    $company->update([
                        'safe2pay_balance' => $safe2payBalance - $refundValue
                    ]);
                }
                
                $refundTransaction->status = 'chargedback';
                $refundTransaction->status_enum = Transaction::STATUS_CHARGEBACK;                
                $refundTransaction->save();
            }

            $sale->update(
                [
                    'status' => Sale::STATUS_CHARGEBACK,
                    'gateway_status' => 'CHARGEBACK',
                ]
            );
            
            $saleContestation->update([
                'status'=>SaleContestation::STATUS_LOST
            ]);            

            $blockSale = BlockReasonSale::where('sale_id',$sale->id)->first();
            if(!empty($blockSale)){
                $blockSale->update([
                    'status'=>BlockReasonSale::STATUS_UNLOCKED
                ]); 
            }
        }
    }

    public function createFakeTicket()
    {
        $sales = Sale::select('sales.id','sales.customer_id')
                ->leftJoin('tickets as c','sales.id','=','c.sale_id')
                ->whereNull('c.id')
                ->where('sales.gateway_id',Gateway::SAFE2PAY_PRODUCTION_ID)
                ->where('sales.status',Sale::STATUS_APPROVED)
                ->inRandomOrder()
                ->limit(1)
                ->get(); 
                
        foreach($sales as $sale){
            Ticket::factory()
            ->for($sale)
            ->create([
                'customer_id'=>$sale->customer_id
            ]);
        }
    }

    public function generateApisFakeData()
    {
        $project = Project::select('id')->inRandomOrder()->first();

        NotazzIntegration::factory()->for($project)->create();
        HotzappIntegration::factory()->for($project)->create();
        ShopifyIntegration::factory()->for($project)->create();
        ConvertaxIntegration::factory()->for($project)->create();
        ActivecampaignIntegration::factory()->for($project)->create();
        Whatsapp2Integration::factory()->for($project)->create();
        ReportanaIntegration::factory()->for($project)->create();
        UnicodropIntegration::factory()->for($project)->create();
        SmartfunnelIntegration::factory()->for($project)->create();
        WooCommerceIntegration::factory()->for($project)->create();
        MelhorenvioIntegration::factory()->create();
        HotbilletIntegration::factory()->for($project)->create();
        AstronMembersIntegration::factory()->for($project)->create();
        ApiToken::factory()->create();
    }

    public function createInvitation()
    {        
        $user =  User::factory()->count(1)->create()->first();

        $user->update([
            'acount_owner_id'=>$user->id
        ]);

        Invitation::factory(1)->for($user)->create();
    }

    public function createAffiliates(){
        $project = Project::select('id')->inRandomOrder()->first();
        $user = User::select('id')->where('id','<>',User::DEMO_ID)->inRandomOrder()->first();

        Affiliate::factory()->for($user)->create([
            'project_id'=>$project->id
        ]);
    }

    public function verifyAbandonedCarts(){
        $sales = DB::table('sales')->select('id','checkout_id')->where('status',Sale::STATUS_CANCELED)->where('start_date','<=',Carbon::now()->subDay())->get();
        foreach($sales as $sale){
            Checkout::find($sale->checkout_id)->update([
                'status'=>'abandoned',
                'status_enum'=>Checkout::STATUS_ABANDONED_CART
            ]);
        }
    }
}