<?php

namespace App\Console\Commands\Demo;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\CheckoutPlan;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Log as CheckoutLog;

class CreateFakeCheckout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "demo:create-fake-checkout";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

    protected $project = null;
    protected $checkout = null;
    protected $total = 0;

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
        /* WARNING
        APÓS ENTRAR EM PRODUÇÃO SÓ DEVE GERAR 1 POR VEZ
        */

        Config::set("database.default", "demo");

        try {
            $isRandomData = false;
            $attemps = 1;
            $counter = 1;

            do {
                $this->createCheckout($isRandomData)
                    ->createCheckoutPlan()
                    ->createCheckoutLog();

                $this->line($counter . "/" . $attemps);
                $counter++;
            } while ($counter <= $attemps);
        } catch (Exception $e) {
            report($e);
        }
    }

    public function createCheckout($isRandomData = false)
    {
        $this->project = DB::table("projects")
            ->select("id")
            ->inRandomOrder()
            ->first();
        $checkoutConfig = DB::table("checkout_configs")
            ->select("checkout_type_enum")
            ->where("project_id", $this->project->id)
            ->first();
        $data = $isRandomData ? Carbon::now()->subDays(rand(1, 60)) : now();

        $this->checkout = Checkout::factory()
            ->count(1)
            ->create([
                "project_id" => $this->project->id,
                "template_type" => (int) $checkoutConfig->checkout_type_enum,
                "created_at" => $data,
                "updated_at" => $data,
            ])
            ->first();

        return $this;
    }

    public function createCheckoutPlan()
    {
        $limit = 1;
        if (mt_rand(1, 7) === 1) {
            $limit = mt_rand(2, 3);
        }
        $plans = Plan::with(["productsPlans.product"])
            ->where("project_id", $this->project->id)
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        $this->total = 0;
        foreach ($plans as $plan) {
            CheckoutPlan::factory(1)
                ->for($this->checkout)
                ->create([
                    "plan_id" => $plan->id,
                    "amount" => 1,
                    "created_at" => $this->checkout->created_at,
                    "updated_at" => $this->checkout->created_at,
                ]);

            $this->total += $plan->price;
        }

        return $this;
    }

    public function createCheckoutLog()
    {
        CheckoutLog::create([
            "checkout_id" => $this->checkout->id,
            "event" => "first access",
            "total_value" => 'R$' . number_format($this->total, 2, ",", "."),
        ]);
        return $this;
    }
}
