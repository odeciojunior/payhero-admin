<?php

namespace Modules\Core\Services\DemoAccount;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\CustomerCard;
use Modules\Core\Entities\Delivery;
use Modules\Core\Entities\DiscountCoupon;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\OrderBumpRule;
use Modules\Core\Entities\PixCharge;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\ProjectUpsellRule;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleInformation;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\User;
use Modules\Core\Services\CacheService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\FoxUtilsFakeService;
use Modules\Core\Services\InstallmentsService;
use Modules\Core\Services\DemoAccount\DemoSplitPayment;

trait DemoPaymentFlowTrait
{
    protected $company = null;

    protected $checkout = null;

    protected $customer = null;

    protected $delivery = null;

    protected $plans = null;

    protected $project = null;

    protected $checkoutConfig = null;

    protected $shipping = null;

    protected $subTotal = 0;

    protected $totalValue = 0;

    protected $payment_method = 0;

    protected $progressiveDiscount = 0;

    protected $shippingPrice = 0;

    protected $installment_amount = 0;

    protected $installmentFreeTaxValue = 0;

    protected $automaticDiscount = 0;

    protected $cupomCode = '';

    protected $onlyDigitalProducts = false;

    protected $hasOrderBump = false;

    protected $upsellPreviousSaleId = 0;

    protected $isUpsell = false;

    public $nextIsUpsell = false;


    public $sale = null;

    public function resetVars()
    {        
        $this->checkout = null;
        $this->customer = null;
        $this->delivery = null;
        $this->plans = null;
        $this->project = null;
        $this->checkoutConfig = null;
        $this->shipping = null;
        $this->subTotal = 0;
        $this->totalValue = 0;
        $this->payment_method = 0;
        $this->progressiveDiscount = 0;
        $this->shippingPrice = 0;
        $this->installment_amount = 0;
        $this->installmentFreeTaxValue = 0;
        $this->automaticDiscount = 0;
        $this->cupomCode = '';
        $this->onlyDigitalProducts = false;
        $this->hasOrderBump = false; 
        $this->sale = null;

        return $this;
    }
    
    public function validateCheckoutLogs(){
         
        $this->checkout = Checkout::whereIn('status_enum',[
            Checkout::STATUS_ABANDONED_CART,Checkout::STATUS_ACCESSED
        ])->inRandomOrder()->first();
        
        $this->project = $this->checkout->project;
        $this->checkoutConfig = $this->project->checkoutConfig;
          
        return $this;
    }

    public function preparePlans()
    {        
        $this->subTotal = 0;
        $planIds = $this->checkout->checkoutPlans()->pluck('plan_id');
        
        $this->plans = Plan::with(['productsPlans.product'])
        ->whereIn('id',$planIds)->get();        
        
        foreach($this->plans as $plan){
            $this->subTotal += FoxUtils::onlyNumbers($plan->price) * 1;     
            
            //desconto do upsell
            if ($this->isUpsell)
            {
                $upsellRule = $this->getUpsellRule($plan->id);

                if (!empty($upsellRule)) {
                    if (is_numeric($upsellRule->discount) && $upsellRule->discount <= 100) {
                        $this->subTotal -= intval($this->subTotal * $upsellRule->discount / 100);
                    }
                }
            }

        }

        $this->totalValue = $this->subTotal;
        return $this;
    }

    private function getUpsellRule($currentPlan)
    {
        return ProjectUpsellRule::where('project_id', $this->project->id)
                ->where('active_flag', true)
                ->where(function ($query) use ($currentPlan) {
                   $query->whereJsonContains('apply_on_plans', $currentPlan)
                   ->orWhereJsonContains('apply_on_plans', 'all');
                })->first();
    }

    private function getOrderBump()
    {
        $currentPlans = $this->plans->pluck('id');
        
        $project = $this->project;
        $applyOnPlansKey = $project->id .'-'. implode('-', $currentPlans->toArray());
        $rules = CacheService::remember(function () use ($project, $currentPlans) {
            return OrderBumpRule::where('project_id', $project->id)
                ->where('active_flag', true)
                ->where(function ($query) use ($currentPlans) {
                    foreach ($currentPlans as $planId) {
                        $query->orWhereJsonContains('apply_on_plans', $planId);
                    }
                    $query->orWhereJsonContains('apply_on_plans', 'all');
                })->get();
        }, CacheService::CHECKOUT_OB_RULES, $applyOnPlansKey);
        
        if (!$rules->count()) return null;

        $rulesArray = [];
        foreach ($rules as $rule) {

            $onlyDigitalProducts = $this->onlyDigitalProducts;
            $plans = CacheService::remember(function () use ($onlyDigitalProducts, $rule) {
                $plansQuery = Plan::with([
                    'productsPlans.product',
                    'variants'
                ])->whereIn('id', $rule->offer_plans);
                if ($onlyDigitalProducts) {
                    $plansQuery->whereDoesntHave('products', function ($query) {
                        $query->where('type_enum', Product::TYPE_PHYSICAL);
                    });
                }
                return $plansQuery->get();
            }, CacheService::CHECKOUT_OB_RULE_PLANS, $rule->id);

            if (!$plans->count()) continue;

            foreach ($plans as $plan) {

                if (empty($rulesArray[$rule->id])) {
                    $rulesArray[$rule->id] = [];
                }
                $rulesArray[$rule->id][] = $plan->id;
            }
        }
       
        return $rulesArray;
    }

    public function prepareOrderBump()
    {              
        $this->hasOrderBump = false;

        if(!$this->keepGoing(10) || $this->isUpsell){            
            return $this;
        }        

        $rulesArray = $this->getOrderBump();

        if(empty($rulesArray)){
            return $this;
        }

        $rules = OrderBumpRule::whereIn('id', array_keys($rulesArray))->get();

        $limitOrderBump = mt_rand(1,3);
        $counterOrderBump = 0;

        foreach ($rules as $rule)
        {
            $selectedPlans = $rulesArray[$rule->id];
            $plansOb = Plan::whereIn('id', $selectedPlans)->get();

            foreach ($plansOb as $planOb) {
                $alreadyInArray = false;

                foreach ($this->plans as $plan) {
                    if ($planOb->id === $plan->id) {
                        $alreadyInArray = true;
                        break;
                    }
                }

                if (!$alreadyInArray && $counterOrderBump < $limitOrderBump) {
                    $this->plans[] = $plan;                                        
                    $price = number_format(
                        floatval($plan->price) - (floatval($plan->price) * $rule->discount / 100),
                        2
                    );
                    $this->subTotal += FoxUtils::onlyNumbers($price);

                    $counterOrderBump++;
                }
            }
        }

        if ($rules->count()) {
            $this->hasOrderBump = true;
            $this->totalValue = $this->subTotal;            
        }
        
        return $this;
    }

    public function prepareData()
    {
        $paymentMethods = [Sale::PAYMENT_TYPE_CREDIT_CARD,Sale::PAYMENT_TYPE_BANK_SLIP,Sale::PAYMENT_TYPE_PIX];
        $this->payment_method = $paymentMethods[ $this->nextIsUpsell ? 0 : rand(0,2) ];        
        
        $this->installment_amount  = 0;
        if($this->payment_method == Sale::PAYMENT_TYPE_CREDIT_CARD){
            $this->installment_amount = mt_rand(1,12);
        }
        
        return $this;
    }

    public function checkAutomaticDiscount()
    {        
        if($this->isUpsell){
            return $this;
        }
        
        switch ($this->payment_method) {
            case Sale::PAYMENT_TYPE_CREDIT_CARD:
                if (!empty($this->checkoutConfig->automatic_discount_credit_card)) {
                    $this->automaticDiscount = intval(
                        $this->subTotal * ($this->checkoutConfig->automatic_discount_credit_card / 100)
                    );
                }
            break;
            case Sale::PAYMENT_TYPE_BANK_SLIP:
                if (!empty($this->checkoutConfig->automatic_discount_bank_slip)) {
                    $this->automaticDiscount = intval(
                        $this->subTotal * ($this->checkoutConfig->automatic_discount_bank_slip / 100)
                    );
                }
                break;
            case Sale::PAYMENT_TYPE_PIX:
                if (!empty($this->checkoutConfig->automatic_discount_pix)) {
                    $this->automaticDiscount = intval(
                        $this->subTotal * ($this->checkoutConfig->automatic_discount_pix / 100)
                    );
                }
            break;
        }

        $this->totalValue -= $this->automaticDiscount;                
        return $this;
    }

    public function setShipping()
    {
        $this->shipping = Shipping::select('value')->where('project_id',$this->project->id)->inRandomOrder()->first();
        $this->shippingPrice = FoxUtils::onlyNumbers($this->shipping->value);
        $this->totalValue+= $this->shippingPrice;                
        return $this;
    } 
    
    public function checkDiscountCoupon()
    {
        if(!$this->keepGoing(8) || $this->isUpsell){
            return $this;
        }
        
        $discountCoupon = DiscountCoupon::where('project_id', $this->project->id)->where('status',1)->inRandomOrder()->first();
        
        if (!empty($discountCoupon) && $this->totalValue > ($discountCoupon->rule_value ?? 0)) {
            $this->cupomCode = $discountCoupon->code;
            $discountValue = $this->applyDiscount($discountCoupon, $this->subTotal);
            $this->totalValue -= $discountValue;            
        }       
        
        return $this;
    }

    public function checkProgressiveDiscount()
    {        
        $this->progressiveDiscount = 0;

        if(!$this->keepGoing(6) || $this->isUpsell){
            return $this;
        }
        
        $this->progressiveDiscount = intval((mt_rand(1,30)/100)*$this->subTotal);

        if($this->progressiveDiscount > 0){
            if($this->totalValue - $this->progressiveDiscount > 500){
                $this->totalValue -= $this->progressiveDiscount;                                
            }else{
                $this->progressiveDiscount = 0;
            }
        }        
        
        return $this;
    }

    public function calculateValues()
    {    
        if ($this->totalValue < 500) {
            response()->json(['message' => 'Valor mínimo de R$ 5,00'], 400)->send();
            exit;
        }

        if ($this->payment_method == Sale::PAYMENT_TYPE_CREDIT_CARD) {

            $installments_interest_free = $this->checkoutConfig->interest_free_installments;

            $installmentsData = InstallmentsService::getFullInstallmentValue(
                $this->totalValue,
                $this->installment_amount,
                $installments_interest_free,
                $this->company->installment_tax
            );

            $interestValue = 0;

            if ($installments_interest_free > 1 && $this->installment_amount <= $installments_interest_free) {
                $this->installmentFreeTaxValue = $installmentsData['total_value_with_tax'] - $this->totalValue;
            } else {
                $interestValue = $installmentsData['total_value_with_tax'] - $this->totalValue;                                
                $this->totalInterestValue = $installmentsData['total_value_with_tax'] - $this->totalValue;
                $this->totalValue = $installmentsData['total_value_with_tax'];
            }

            $this->installmentsValue = $installmentsData['installment_value'];
            $this->cloudfoxValue = ((int)(($this->totalValue - $interestValue) / 100 * $this->company->gateway_tax));
            $this->cloudfoxValue += str_replace('.', '', $this->company->transaction_rate);
            $this->cloudfoxValue += $interestValue;
        } else {
            $this->cloudfoxValue = (int)(($this->totalValue / 100) * $this->company->gateway_tax);

            if (($this->totalValue / 100) <= 40) {
                $transactionRate = 300;
            } else {
                $transactionRate = $this->company->transaction_rate;
            }
            $this->cloudfoxValue += FoxUtils::onlyNumbers($transactionRate);
        }

        return $this;
    }

    public function setCustomer()
    {
        try {
            $this->customer = Customer::factory()->create();        
            $this->delivery = Delivery::factory()->for($this->customer)->create();

        } catch (Exception $e) {            
             
        } 

        return $this;
    }

    public function getUserInvitation()
    {
        if($this->isUpsell){
            $salePrevious = DB::table('sales')->select('owner_id')->where('id',$this->upsellPreviousSaleId)->first();
            return $salePrevious->owner_id;
        }
        /*

        $withInvite = $this->keepGoing(6);
        $userId = User::DEMO_ID;

        if($withInvite){
            $invite = Invitation::select('user_invited')->where('invite', $userId)
            ->where('status', Invitation::STATUS_ACTIVE)->inRandomOrder()->first();
            if(!empty($invite)){
                $userId = $invite->user_invited;                
            }
        }*/

        return User::DEMO_ID;
    }

    public function setSale($isRandomData=false)
    {        
        $data = $isRandomData?Carbon::now()->subDays(rand(1,60)):now();
        $this->sale = Sale::factory()->create(
            [
                'progressive_discount' => $this->progressiveDiscount,
                'owner_id' => $this->getUserInvitation(),
                'customer_id' => $this->customer->id,
                'project_id' => $this->project ? $this->project->id : null,
                'shipping_id' => $this->shipping->id,
                'checkout_id' => $this->checkout ? $this->checkout->id : null,
                'affiliate_id' => $this->project ? FoxUtils::getCookieAffiliate($this->project->id) : null,
                'payment_method' => $this->payment_method,
                'total_paid_value' => number_format(intval($this->totalValue)/100,2,'.',''),
                'original_total_paid_value' => $this->totalValue,
                'sub_total' => FoxUtils::floatFormat($this->subTotal),
                'shipment_value' => FoxUtils::floatFormat($this->shippingPrice),
                'cupom_code' => $this->cupomCode,                
                'gateway_transaction_id' => '',                
                'installments_amount' => $this->payment_method == Sale::CREDIT_CARD_PAYMENT ? $this->installment_amount : null,
                'installments_value' => $this->payment_method == Sale::CREDIT_CARD_PAYMENT
                    ? FoxUtils::floatFormat($this->totalValue/$this->installment_amount)
                    : null,                
                'delivery_id' => !empty($this->delivery) ? $this->delivery->id : null,
                'shopify_discount' => '0',
                'installment_tax_value' => $this->payment_method == Sale::CREDIT_CARD_PAYMENT ? $this->installmentFreeTaxValue: null,
                'upsell_id' => null,
                'automatic_discount' => $this->automaticDiscount,
                'interest_total_value' => $this->totalInterestValue ?? 0,
                'has_order_bump' => $this->hasOrderBump,                
                'status'=>$this->nextIsUpsell || $this->isUpsell ? Sale::STATUS_APPROVED : FoxUtilsFakeService::getRandomSaleStatus($this->payment_method),
                'start_date'=>$data,
                'created_at'=>$data,
                'updated_at'=>$data
            ]            
        );

        foreach ($this->plans as $key => $plan) {
            
            PlanSale::create(
                [
                    'plan_id' => $plan->id,
                    'sale_id' => $this->sale->id,
                    'amount' => 1,
                    'plan_value' => $plan->price,
                    'created_at'=>$data,
                    'updated_at'=>$data
                ]
            );

            foreach ($plan->productsPlans as $productPlan) {
                $product = $productPlan->product;

                $amount = $productPlan ? $productPlan->amount * 1 : null;

                ProductPlanSale::create(
                    [
                        'product_id' => $product->id,
                        'plan_id' => $plan->id,
                        'sale_id' => $this->sale->id,
                        'amount' => $amount,
                        'cost' => $productPlan->cost ?? 0,
                        'name' => $product->name,
                        'digital_product_url' => $product->digital_product_url ?? null,
                        'created_at'=>$data,
                        'updated_at'=>$data
                    ]
                );
            }
        }

        $this->customer->update([
            'created_at'=>$data,
            'updated_at'=>$data
        ]);

        $this->delivery->update([
            'created_at'=>$data,
            'updated_at'=>$data
        ]);

        return $this;
    }

    public function executePayment()
    {
        switch($this->payment_method)
        {
            case Sale::PAYMENT_TYPE_CREDIT_CARD:
                $card = CustomerCard::factory()
                    ->for($this->customer)
                    ->create([
                        'created_at'=>$this->sale->created_at,
                        'updated_at'=>$this->sale->updated_at,
                    ]); 

                SaleInformation::factory()->for($this->sale)->create([
                    'installments'=>$this->sale->installments_amount,
                    'first_six_digits' => $card->first_six_digits,
                    'last_four_digits' => $card->last_four_digits,
                    'card_token' => $card->card_token,
                    'created_at'=>$this->sale->created_at,
                    'updated_at'=>$this->sale->updated_at
                ]);

                $this->sale->update([
                    'flag'=>FoxUtilsFakeService::getRandoFlagCC(),
                    'upsell_id'=> $this->isUpsell && $this->sale->status==Sale::STATUS_APPROVED ? $this->upsellPreviousSaleId:null,
                ]);
            break;
            case Sale::PAYMENT_TYPE_BANK_SLIP:
                SaleInformation::factory()->for($this->sale)
                ->create([
                    'created_at'=>$this->sale->created_at,
                    'updated_at'=>$this->sale->updated_at
                ]);
                $this->sale->update([
                    'boleto_digitable_line'=>'99999999999999999999999999999999999999999999999',
                    'gateway_billet_identificator'=>'23793899400000171000558099999999999900139780',
                    'boleto_due_date'=>Carbon::now()->addDays(3)->format('Y-m-d H:i:s')
                ]);
                break;
            case Sale::PAYMENT_TYPE_PIX:
                SaleInformation::factory()->for($this->sale)
                ->create([
                    'created_at'=>$this->sale->created_at,
                    'updated_at'=>$this->sale->updated_at
                ]);

                $pixCharge = PixCharge::factory()->for($this->sale)
                ->create([
                    'created_at'=>$this->sale->created_at,
                    'updated_at'=>$this->sale->updated_at
                ]);
                $pixCharge->update([
                    'status'=>$this->sale->status ==Sale::STATUS_APPROVED ? 'RECEBIDO' : 'ATIVA'
                ]);

            break;
        }
        return $this;
    }

    public function setTransactions()
    {        
        DemoSplitPayment::perform($this->sale);

        if($this->sale->status == Sale::STATUS_APPROVED){
            $this->sale->update([
                'end_date'=>Carbon::now()
            ]);
        }
        return $this;
    }

    public function applyDiscount(DiscountCoupon $discountCoupon, $totalValue)
    {
        try {$totalValue = intval($totalValue);
            if (!empty($discountCoupon)) {
                
                if ($discountCoupon->type == 1) {
                    if (($totalValue - $discountCoupon->value) < 500) {
                        return 0;
                    }

                    return $discountCoupon->value;                    
                } else {
                    $discount = intval($totalValue * ($discountCoupon->value / 100));
                    if (($totalValue - $discount) < 500) {
                        return 0;
                    } 

                    $discount = str_replace('.', '', $discount);
                    return $discount;                    
                }
            } 

            return 0;

        } catch (Exception $e){            
            report($e);
            return 0;
        }
    }

    public function setTranking()
    {
        if($this->sale->status == Sale::STATUS_APPROVED)
        {
            $productPlanSales = DB::table('products_plans_sales')->select('id','product_id')->where('sale_id',$this->sale->id)->get();
            foreach($productPlanSales as $item){
                Tracking::factory()
                ->count(1)
                ->for($this->sale)
                ->create([
                    'product_plan_sale_id'=>$item->id,
                    'product_id'=>$item->product_id,
                    'amount'=>$this->sale->shipment_value*100,
                    'delivery_id'=>$this->sale->delivery_id,
                ]);
            }
        }

        return $this;
    }

    public function keepGoing($maxPossibility = 7)
    {
        return mt_rand(1,$maxPossibility) === 1;
    }

}
