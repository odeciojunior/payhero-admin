<?php

namespace App\Console\Commands\Sac;

use Illuminate\Console\Command;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketMessage;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\SmsService;

class NotifyMediation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "notify:mediation";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Notifica os usuários informando que podem solicitar mediação";

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
     * @return void
     */
    public function handle()
    {
        $sendGridService = new SendgridService();
        $smsService = new SmsService();

        $daysWithoutUserResponse = 5;

        $query = Ticket::with(["sale.customer", "sale.project"])
            ->where("ticket_status_enum", Ticket::STATUS_OPEN)
            ->where("ticket_category_enum", Ticket::CATEGORY_COMPLAINT)
            ->where("mediation_notified", 0)
            ->where("last_message_date", "<", now()->subDays($daysWithoutUserResponse))
            ->where("last_message_type_enum", TicketMessage::TYPE_FROM_CUSTOMER);

        $bar = $this->getOutput()->createProgressBar($query->count());
        $bar->start();

        $query->chunk(500, function ($tickets) use ($bar, $sendGridService, $smsService) {
            foreach ($tickets as $ticket) {
                try {
                    $projectName = '';
                    if (!empty($ticket->sale->project)) {
                        $projectName = $ticket->sale->project->name ?? '';
                    }

                    $customer = $ticket->sale->customer ?? null;
                    $customerName = current(explode(" ", $customer->name));
                    $customerEmail = $customer->email ?? null;

                    if (empty($customerEmail)) {
                        $data = [
                            "name" => $customerName,
                            "project" => $projectName,
                        ];

                        $sendGridService->sendEmail(
                            "noreply@azcend.com.br",
                            "Azcend",
                            $customerEmail,
                            $customerName,
                            "d-fcf27621590d4c448ed9a2b3666f4de2", /// done
                            $data
                        );
                    } else {
                        $smsService->sendSms(
                            $customer->telephone,
                            "Olá {$customerName}, podemos ajudar a solucionar a sua reclamação. Acesse https://sac.azcend.com.br e solicite mediação."
                        );
                    }

                    $ticket->mediation_notified = 1;
                    $ticket->save();
                } catch (\Exception $e) {
                    report($e);
                }
                $bar->advance();
            }
        });

        $bar->finish();
    }
}
