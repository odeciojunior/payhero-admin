<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\CheckoutPlan;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\CustomerCard;
use Modules\Core\Entities\Delivery;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Shipping;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\InstallmentsService;
use Modules\Core\Services\SaleService;

class CreateSalesFakeForDemoAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo-account:create-sales-fake';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $company = null;

    protected $checkout = null;

    protected $customer = null;

    protected $delivery = null;

    protected $plans = null;

    protected $project = null;

    protected $checkoutConfig = null;

    protected $subTotal = 0;

    protected $totalValue = 0;

    protected $payment_method = 0;

    protected $progressiveDiscount = 0;

    protected $shippingPrice = 0;

    protected $installment_amount = 0;

    protected $installmentFreeTaxValue = 0;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {        
        Config::set('database.default', 'demo');

        $this->company = Company::find(Company::COMPANY_ID_DEMO);

        $this->createCheckout()
            ->preparePlans()        
            ->prepareData()
            ->checkAutomaticDiscount()
            ->setCustomer()
            ->setShipping()    
            ->checkProgressiveDiscount()        
            ->calculateValues()
            ->setSale()
            ->executePayment();                        
    }

    public function createCheckout(){
        try { 
            $this->project = Project::with('checkoutConfig')->inRandomOrder()->first();
            $this->checkoutConfig = $this->project->checkoutConfig;
            
            DB::beginTransaction();
            $this->checkout = Checkout::factory()
            ->state([                
                'project_id'=>$this->project->id,
                'template_type'=>$this->checkoutConfig->checkout_type_enum
            ])            
            ->count(1)
            ->create();

            DB::commit();
            
            return $this;

        } catch (Exception $e) {
            \Log::info($e->getMessage());
            DB::rollBack();
        }   
    }

    public function preparePlans()
    {       
        $this->plans = Plan::with(['productsPlans.product'])
        ->where('project_id',$this->project->id)
        ->inRandomOrder()->limit(Rand(1,4))->get();

        $this->checkoutConfig = $this->project->checkoutConfig;
        
        foreach($this->plans as $plan){
            $this->subTotal += FoxUtils::onlyNumbers($plan->price) * 1;
            CheckoutPlan::factory()->state(['plan_id'=>$plan->id])->count(1)->create();
        }
        $this->totalValue = $this->subTotal;

        return $this;
    }

    public function prepareData(){
        $paymentMethods = [Sale::PAYMENT_TYPE_CREDIT_CARD,Sale::PAYMENT_TYPE_BANK_SLIP,Sale::PAYMENT_TYPE_PIX];
        $this->payment_method = $paymentMethods[Rand(0,2)];        
        
        $this->installment_amount  = 0;
        if($this->payment_method == Sale::PAYMENT_TYPE_CREDIT_CARD){
            $this->installment_amount = Rand(1,12);
        }

        return $this;
    }

    public function checkAutomaticDiscount()
    {
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
        
        return $this;
    }

    public function setShipping()
    {
        $shipping = Shipping::select('value')->where('project_id',$this->project_id)->inRandomOrder()->first();
        $this->shippingPrice = FoxUtils::onlyNumbers($shipping->value);
        $this->totalValue+= $this->shippingPrice;
        return $this;
    }    

    public function checkProgressiveDiscount(){
        $this->progressiveDiscount = 0;

        if(Rand(0,1))
        {
            $this->progressiveDiscount = (Rand(1,30)/100)*$this->subTotal;
    
            if($this->progressiveDiscount > 0){
                if($this->totalValue - $this->progressiveDiscount > 500){
                    $this->totalValue -= $this->progressiveDiscount;
                }else{
                    $this->progressiveDiscount = 0;
                }
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
            DB::beginTransaction();
            $this->customer = Customer::factory()
            ->count(1)            
            ->create();        

            $this->delivery = Delivery::factory()->count(1)->for($this->customer)->create();

            DB::commit();

            return $this;

        } catch (Exception $e) {
            \Log::info($e->getMessage());
            DB::rollBack();
        } 
    }

    public function setSale(){
        $this->sale = Sale::factory()->state(
            [
                'progressive_discount' => $this->progressiveDiscount,
                'owner_id' => Company::USER_ID_DEMO,
                'customer_id' => $this->customer->id,
                'project_id' => $this->project ? $this->project->id : null,
                'shipping_id' => @$this->shipping->id,
                'checkout_id' => $this->checkout ? $this->checkout->id : null,
                'affiliate_id' => $this->project ? FoxUtils::getCookieAffiliate($this->project->id) : null,
                'payment_method' => $this->payment_method,
                'total_paid_value' => FoxUtils::floatFormat($this->totalValue),
                'original_total_paid_value' => $this->totalValue,
                'sub_total' => FoxUtils::floatFormat($this->subTotal),
                'shipment_value' => FoxUtils::floatFormat($this->shippingPrice),
                'cupom_code' => '',
                'start_date' => Carbon::now(),
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
                'has_order_bump' => 0,
                'status'=>Sale::STATUS_PENDING
            ]            
        );

        foreach ($this->plans as $key => $plan) {
            
            PlanSale::create(
                [
                    'plan_id' => $plan->id,
                    'sale_id' => $this->sale->id,
                    'amount' => 1,
                    'plan_value' => $plan->price,
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
                    ]
                );
            }
        }

        $this->sale->load(['productsPlansSale', 'plansSales']);

        SaleService::createSaleLog($this->sale->id, $this->sale->status);
       
        return $this;
    }

    public function executePayment(){
        switch($this->payment_method){
            case Sale::PAYMENT_TYPE_CREDIT_CARD:
                $this->creditCardPayment();
            break;
            case Sale::PAYMENT_TYPE_BANK_SLIP:
                $this->bankSlipPayment();
                break;
            case Sale::PAYMENT_TYPE_PIX:
                $this->pixPayment();
            break;
        }
    }

    public function creditCardPayment()
    {
        CustomerCard::factory()
                    ->count(1)
                    ->for($this->customer)
                    ->create();
                    
        $status  = $this->getRandomStatus();
    }

    public function bankSlipPayment(){

    }    

    public function pixPayment(){
        return $this;
    }

    public function getRandomStatus(){
        $status = [Sale::STATUS_PENDING, Sale::STATUS_APPROVED,Sale::STATUS_CANCELED];

        if($this->paymentMethod == Sale::CREDIT_CARD_PAYMENT){
            $status = [Sale::STATUS_APPROVED,Sale::STATUS_CANCELED_ANTIFRAUD, Sale::STATUS_IN_REVIEW,sale::STATUS_REFUSED];
        }
        return Arr::random($status);
    }

}
