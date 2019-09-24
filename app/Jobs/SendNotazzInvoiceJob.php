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
        $notazzService      = new NotazzService();
        $notazzInvoiceModel = new NotazzInvoice();

        $notazzInvoice = $notazzInvoiceModel->find($this->notazzInvoiceId);

        if ($notazzInvoice->attempts <= $notazzInvoice->max_attempts) {
            //ainda nao chegou no maximo de tentativas

            $notazzInvoice->update([

                                       'status' => $notazzInvoiceModel->present()
                                                                      ->getStatus('in_process'),

                                   ]);

            if ($notazzInvoice->invoice_type == $notazzInvoiceModel->present()->getInvoiceType('service')) {
                //nota de servico

                $result = $notazzService->sendNfse($this->notazzInvoiceId);
            } else if ($notazzInvoice->invoice_type == $notazzInvoiceModel->present()->getInvoiceType('product')) {
                //nota de produto

                //$result = $notazzService->sendNfse($this->notazzInvoiceId);
            } else {
                //erro ?

                $notazzInvoice->update([
                                           'return_message'   => 'Invoice Type Error',
                                           'return_http_code' => '500',
                                           'schedule'         => $notazzInvoice->schedule,
                                           'date_error'       => Carbon::now(),
                                           'status'           => $notazzInvoiceModel->present()
                                                                                    ->getStatus('error'), //error
                                           'attempts'         => $notazzInvoice->max_attempts + 1,

                                       ]);

                Log::warning('Type da invoice invalido (SendNotazzInvoiceJob - handle)');
                report(new Exception('NotazzInvoice - type da invoice invalido, invoice : ' . $notazzInvoice->id));
            }

            if (!empty($result)) {
                if (($result->codigoProcessamento >= 200) && ($result->codigoProcessamento <= 299)) {
                    //200 code
                    $notazzInvoice->update([
                                               'return_message'   => $result->motivo,
                                               'return_http_code' => $result->codigoProcessamento,
                                               'date_sent'        => Carbon::now(),
                                               'status'           => $notazzInvoiceModel->present()
                                                                                        ->getStatus('send'), //send

                                           ]);
                } else {
                    //qualquer outro erro, remarcar invoice para ser enviado depois

                    $notazzInvoice->update([
                                               'return_message'   => $result->motivo,
                                               'return_http_code' => $result->codigoProcessamento,
                                               'schedule'         => Carbon::now()
                                                                           ->addHour(),
                                               'date_error'       => Carbon::now(),
                                               'status'           => $notazzInvoiceModel->present()
                                                                                        ->getStatus('error'), //error
                                           ]);
                }
            } else {
                //venda invalida
                $notazzInvoice->update([
                                           'return_message'   => 'Venda não localizada',
                                           'return_http_code' => '500',
                                           'schedule'         => $notazzInvoice->schedule,
                                           'date_error'       => Carbon::now(),
                                           'status'           => $notazzInvoiceModel->present()
                                                                                    ->getStatus('error'), //error
                                           'attempts'         => $notazzInvoice->max_attempts + 1,

                                       ]);
            }
        } else {
            //chegou no maximo de tentativa, report e alertar vendedor?
            //TODO alertar vendedor

            Log::warning('Maximo de tentativas para o envio da invoice (SendNotazzInvoiceJob - handle)');
            report(new Exception('NotazzInvoice - Maximo de tentativas, invoice : ' . $notazzInvoice->id));
        }
    }
}
