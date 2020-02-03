<?php

namespace App\Jobs;

use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\NotazzInvoice;
use Modules\Core\Services\NotazzService;

/**
 * Class SendNotazzInvoiceJob
 * @package App\Jobs
 */
class SendNotazzInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $notazzInvoiceId;

    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct($notazzInvoiceId)
    {
        $this->notazzInvoiceId = $notazzInvoiceId;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        try {

            $notazzService = new NotazzService();

            $notazzService->sendInvoice($this->notazzInvoiceId);
        } catch (Exception $e) {
            Log::warning('SendNotazzInvoiceJob - Erro no job ');
            report($e);
        }
    }
}
