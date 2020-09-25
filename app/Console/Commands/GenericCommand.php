<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\User;
use Modules\Core\Services\AwsSns;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Core\Services\TrackingmoreService;
use Illuminate\Database\Eloquent\Builder;
use Vinkla\Hashids\Facades\Hashids;


/**
 * Class GenericCommand
 * @package App\Console\Commands
 */
class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'generic {user?}';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        $products = Product::where('type_enum', 2)->whereNull('status_enum')->get();
        foreach ($products as $product) {
            $product->update([
                                 'status_enum' => 1,
                             ]);
        }
        dd('feitoo');
    }
}


