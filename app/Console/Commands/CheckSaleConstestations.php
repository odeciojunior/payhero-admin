<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Chargebacks\Imports\ContestationImport;
use Modules\Core\Services\Email\Gmail\GmailService;
use Illuminate\Support\Facades\Log;

class CheckSaleConstestations extends Command
{
    protected $signature = 'getnet:import-sale-contestations {date_after?}';

    protected $description = 'Import sale contestation from gmail';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        $date_after = $this->argument('date_after');
        $gmailService = new GmailService();
        try {
            $attachmentPaths = $gmailService->getAttachments(50, $date_after, false);

            foreach ($attachmentPaths as $path) {
                $path = str_replace("/storage/", "", $path);
                \Excel::import(new ContestationImport, $path);
            }
            $gmailService->clearFolder();
        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
