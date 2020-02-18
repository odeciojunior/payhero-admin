<?php

use Illuminate\Database\Seeder;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketMessage;

class TicketsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        if (env('APP_ENV', 'local') != 'production') {
            //            $tickets         = Ticket::all();
            //            $ticketPresenter = (new Ticket())->present();
            //            if (count($tickets) == 0) {
            //                $ticket = Ticket::create([
            //                                             'sale_id'              => 134609,
            //                                             'customer_id'          => 2444,
            //                                             'subject'              => 'Atraso na entrega do produto',
            //                                             'description'          => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            //                                             'ticket_category_enum' => $ticketPresenter->getTicketCategoryEnum('complaint'),
            //                                             'ticket_status_enum'   => $ticketPresenter->getTicketStatusEnum('open'),
            //                                         ]);
            //                TicketMessage::create([
            //                                          'ticket_id'  => $ticket->id,
            //                                          'message'    => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. ',
            //                                          'from_admin' => 0,
            //                                      ]);
            //            }
        }
    }
}
