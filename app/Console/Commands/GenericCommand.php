<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketAttachment;
use Modules\Core\Entities\TicketMessage;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingService;
use Vinkla\Hashids\Facades\Hashids;

class GenericCommand extends Command
{
    protected $signature = "generic";
    protected $description = "Command description";

    public function handle()
    {
        $companyId = current(Hashids::decode('KN1nVZplmWZlM6B'));
        dd($companyId);
    }
}
