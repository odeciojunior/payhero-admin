<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserInformation;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class PopulateUserInformations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:userinformations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        try {
            $users = User::where('created_at', '<', '2022-05-26')->whereRaw('account_owner_id = id')->whereNotNull('document')->get();

            $output = new ConsoleOutput();
            $progress = new ProgressBar($output, count($users));
            $progress->start();

            foreach($users as $user) {
                $userInformations = UserInformation::where('document', $user->document)->doesntExist();
                if ($userInformations) {
                    UserInformation::create([
                        'email' => $user->email,
                        'document' => $user->document,
                        'phone' => $user->cellphone,
                        'monthly_income' => '20000',
                        'niche' => '{"others":"1","classes":"0","subscriptions":"0","digitalProduct":"0","physicalProduct":"0","dropshippingImport":"0"}',
                        'website_url' => '',
                        'gateway' => '',
                        'ecommerce' => '{"wix":0,"shopify":0,"pageLand":0,"wooCommerce":0,"otherEcommerce":1,"integratedStore":0,"otherEcommerceName":""}',
                        'cloudfox_referer' => '{"ad":0,"email":0,"other":1,"youtube":0,"facebook":0,"linkedin":0,"instagram":0,"recomendation":0}',
                    ]);
                }

                $progress->advance();
            }

            $this->line($progress);

            $progress->finish();
        } catch(Exception $e) {
            $this->line($e->getMessage());
        }
    }
}
