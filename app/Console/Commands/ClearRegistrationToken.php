<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\User;
use Modules\Register\Entities\RegistrationToken;

class ClearRegistrationToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:registration-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear database registration-token';

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
        $counts = RegistrationToken::whereNotNull('id')->delete();
        $user = User::where('email', '=', 'julioleichtweis@cloudfox.net')->delete();
        echo 'deletados ' . $counts . ' - ' . $user;
    }
}
