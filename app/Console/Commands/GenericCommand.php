<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\ProjectNotificationService;
use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;


/**
 * Class GenericCommand
 * @package App\Console\Commands
 */
class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generic:command';

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
     * @throws PresenterException
     */
    public function handle()
    {
        //
    }

}
