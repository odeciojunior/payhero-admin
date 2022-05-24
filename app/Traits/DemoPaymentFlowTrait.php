<?php

namespace App\Traits;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\CheckoutConfig;
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
use Modules\Core\Entities\SaleInformation;
use Modules\Core\Entities\Shipping;
use Modules\Core\Services\DemoSplitPayment;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\FoxUtilsFakeService;
use Modules\Core\Services\InstallmentsService;
use Modules\Core\Services\SaleService;

trait DemoPaymentFlowTrait
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

    protected $shipping = null;

    protected $subTotal = 0;

    protected $totalValue = 0;

    protected $payment_method = 0;

    protected $progressiveDiscount = 0;

    protected $shippingPrice = 0;

    protected $installment_amount = 0;

    protected $installmentFreeTaxValue = 0;

    protected $automaticDiscount = 0;
    
    public function createCheckout(){
        try { 
            $this->project = Project::inRandomOrder()->first();
            $this->checkoutConfig = CheckoutConfig::where('project_id',$this->project->id)->first();
            
            DB::beginTransaction();
            $this->checkout = Checkout::factory()
            ->count(1)
            ->create([                
                'project_id'=>$this->project->id,
                'template_type'=>(int)$this->checkoutConfig->checkout_type_enum
            ])->first();
            
            DB::commit();            

        } catch (Exception $e) {
            \Log::info($e->getMessage());
            DB::rollBack();
        }
        return $this;
    }

    public function preparePlans()
    {       
        $this->plans = Plan::with(['productsPlans.product'])
        ->where('project_id',$this->project->id)
        ->inRandomOrder()->limit(Rand(1,4))->get();

        $this->checkoutConfig = $this->project->checkoutConfig;
        
        foreach($this->plans as $plan){
            $this->subTotal += FoxUtils::onlyNumbers($plan->price) * 1;
            CheckoutPlan::factory()->for($plan)->create([
                'plan_id'=>$plan->id,
                'amount'=>1,
            ]);
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
        $this->shipping = Shipping::select('value')->where('project_id',$this->project->id)->inRandomOrder()->first();
        $this->shippingPrice = FoxUtils::onlyNumbers($this->shipping->value);
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
            $this->customer = Customer::factory()->create();        

            $this->delivery = Delivery::factory()->for($this->customer)->create();

            DB::commit(); 

        } catch (Exception $e) {
            \Log::info($e->getMessage());
            DB::rollBack();
        } 

        return $this;
    }

    public function setSale()
    {        
        $this->sale = Sale::factory()->create(
            [
                'progressive_discount' => $this->progressiveDiscount,
                'owner_id' => Company::USER_ID_DEMO,
                'customer_id' => $this->customer->id,
                'project_id' => $this->project ? $this->project->id : null,
                'shipping_id' => $this->shipping->id,
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
                'status'=>FoxUtilsFakeService::getRandomStatus($this->payment_method)
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

        return $this;
    }

    public function executePayment(){
        switch($this->payment_method){
            case Sale::PAYMENT_TYPE_CREDIT_CARD:
                $card = CustomerCard::factory()
                    ->for($this->customer)
                    ->create(); 

                SaleInformation::factory()->for($this->sale)->create([
                    'installments'=>$this->sale->installments_amount,
                    'first_six_digits' => $card->first_six_digits,
                    'last_four_digits' => $card->last_four_digits,
                    'card_token' => $card->card_token,
                ]);

                $this->sale->update([
                    'flag'=>FoxUtilsFakeService::getRandoFlagCC()
                ]);
            break;
            case Sale::PAYMENT_TYPE_BANK_SLIP:
                SaleInformation::factory()->for($this->sale)->create();
                $this->sale->update([
                    'boleto_digitable_line'=>'99999999999999999999999999999999999999999999999',
                    'gateway_billet_identificator'=>'23793899400000171000558099999999999900139780',
                    'boleto_due_date'=>Carbon::now()->addDays(3)->format('Y-m-d H:i:s')
                ]);
                break;
            case Sale::PAYMENT_TYPE_PIX:
                SaleInformation::factory()->for($this->sale)->create();
            break;
        }
        return $this;
    }

    public function setTransactions()
    {
        DemoSplitPayment::perform($this->sale);

        if($this->sale->status == Sale::STATUS_APPROVED){
            $this->sale->update([
                'end_date'=>now()
            ]);
        }
        return $this;
    }

}
