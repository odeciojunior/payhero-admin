<?php

namespace Modules\Core\Services;

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
use Modules\Core\Entities\User;

class DemoFakeDataService
{
    public function createFakeContestation(){

        $sales = Sale::select('sales.id')
                ->leftJoin('sale_contestations as c','sales.id','=','c.sale_id')
                ->whereNull('c.id')
                ->where('sales.gateway_id',Gateway::SAFE2PAY_PRODUCTION_ID)
                ->where('sales.status',Sale::STATUS_APPROVED)
                ->inRandomOrder()
                ->limit(3)
                ->get();        

        $blockStatus = BlockReasonSale::STATUS_BLOCKED;
        $blockObs = 'Em disputa';
        foreach($sales as $sale){
            $contestation = SaleContestation::factory()->for($sale)->create();
            switch ($contestation->status) {
                case SaleContestation::STATUS_IN_PROGRESS:
                    $blockStatus = BlockReasonSale::STATUS_BLOCKED;
                    $blockObs = 'Em disputa';
                break;
                case SaleContestation::STATUS_LOST:
                    $blockObs = 'Chargeback';
                    $blockStatus = BlockReasonSale::STATUS_UNLOCKED;
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

    public function createFakeTicket()
    {
        $sales = Sale::select('sales.id','sales.customer_id')
                ->leftJoin('tickets as c','sales.id','=','c.sale_id')
                ->whereNull('c.id')
                ->where('sales.gateway_id',Gateway::SAFE2PAY_PRODUCTION_ID)
                ->where('sales.status',Sale::STATUS_APPROVED)
                ->inRandomOrder()
                ->limit(3)
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
        $user =  User::factory()->count(1)->create();

        $user->update([
            'acount_owner_id'=>$user->id
        ]);

        Invitation::factory()->for($user)->create();

    }

    public function createAffiliates(){
        $project = Project::select('id')->inRandomOrder()->first();
        $user = User::select('id')->where('id','!',User::DEMO_ID)->inRandomOrder()->first();

        Affiliate::factory()->for($user,$project)->create();
    }

    public function verifyAbandonedCarts(){
        $sales = DB::table('sales')->select('id')->where('status',Sale::STATUS_PENDING)->where('start_date','<=',Carbon::now()->subDay())->get();
        foreach($sales as $sale){

        }
    }
}