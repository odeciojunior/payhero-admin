<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\CustomerWithdrawal;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\SaleContestation;
use Modules\Core\Entities\TicketAttachment;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\AmazonFileService;

class updateSaleContestation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updatesalecontestation:save-expiration-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move all files to s3';


    protected $s3Drive;

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
        $sale_contestations = SaleContestation::whereNotNull('file_date')->get();

        foreach($sale_contestations as $sale_contestation){

            $data = json_decode($sale_contestation->data, true);
            $expiration = \Carbon\Carbon::createFromFormat("dmY", $data['Data do Retorno']);
            $sale_contestation->expiration_date = $expiration;
            $sale_contestation->save();

        }


    }

}
