<?php

namespace Modules\Tickets\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketMessage;
use Modules\Core\Services\FoxUtils;
use Modules\Tickets\Transformers\TicketMessageResource;
use Modules\Tickets\Transformers\TicketResource;
use Modules\Tickets\Transformers\TicketShowResource;
use Vinkla\Hashids\Facades\Hashids;

class TicketsApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $ticketsModel = new Ticket();
            $data         = $request->all();
            $userId       = auth()->user()->account_owner_id;
            $tickets      = $ticketsModel->with([
                                                    'messages',
                                                    'customer',
                                                    'sale',
                                                ])
                                         ->whereHas('sale', function($query) use ($userId) {
                                             $query->where('owner_id', $userId);
                                         });
            if (!empty($data['date'])) {
                $date = FoxUtils::validateDateRange($data["date"]);
                $tickets->whereBetween('created_at', [$date[0] . ' 00:00:00', $date[1] . ' 23:59:59']);
            }
            if (!empty($data['status'])) {
                $tickets->where('ticket_status_enum', $ticketsModel
                    ->present()
                    ->getTicketStatusEnum($data['status']));
            }
            if (!empty($data['category'])) {
                $tickets->where('ticket_category_enum', $ticketsModel
                    ->present()
                    ->getTicketCategoryEnum($data['category']));
            }
            if (!empty($data['customer'])) {
                $customerName = $data['customer'];
                $tickets->whereHas('customer', function($query) use ($customerName) {
                    $query->where('name', 'LIKE', '%' . $customerName . '%');
                });
            }
            if (!empty($data['ticket_id'])) {
                $ticketId = current(Hashids::decode($data['ticket_id'] ?? ''));
                $tickets->where('id', $ticketId);
            }
            $tickets = $tickets->paginate(5);

            return TicketResource::collection($tickets);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao carregar chamados'], 400);
        }
    }

    public function show($id)
    {
        try {

            $ticketsModel = new Ticket();

            $ticketId = current(Hashids::decode($id ?? ''));

            if (!empty($ticketId)) {

                $ticket = $ticketsModel->with([
                                                  'sale',
                                                  'customer',
                                                  'messages',
                                                  'attachments',
                                              ])->find($ticketId);

                return new TicketShowResource($ticket);
            } else {
                return response()->json(['message' => 'Chamado não encontrado!'], 400);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao carregar chamado'], 400);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $ticketsModel = new Ticket();

            $ticketId = current(Hashids::decode($id ?? ''));

            $data = $request->all();

            if (!empty($ticketId) && !empty($data['status'])) {

                $ticket = $ticketsModel->find($ticketId);

                $ticket->update([
                                    'ticket_status_enum' => $ticket->present()->getTicketStatusEnum($data['status']),
                                ]);

                return new TicketShowResource($ticket);
            } else {
                return response()->json(['message' => 'Chamado não encontrado!'], 400);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao carregar chamado'], 400);
        }
    }

    public function sendMessage(Request $request)
    {
        try {

            $ticketMessageModel = new TicketMessage();

            $data = $request->all();

            $ticketId = current(Hashids::decode($data['ticket_id'] ?? ''));

            if (!empty($ticketId)) {

                if (!empty($data['message'])) {
                    $message = $ticketMessageModel->create([
                                                               'ticket_id'  => $ticketId,
                                                               'message'    => $data['message'],
                                                               'from_admin' => true,
                                                           ]);

                    return new TicketMessageResource($message);
                } else {
                    return response()->json(['message' => 'Dados inválidos'], 400);
                }
            } else {
                return response()->json(['message' => 'Chamado não encontrado'], 400);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao enviar mensagem'], 400);
        }
    }

    public function getTotalValues(Request $request)
    {
        try {
            $ticketsModel    = new Ticket();
            $data            = $request->all();
            $userId          = auth()->user()->account_owner_id;
            $ticketPresenter = $ticketsModel->present();
            $ticket          = $ticketsModel->selectRaw('count(case when ticket_status_enum = ' . $ticketPresenter->getTicketStatusEnum('open') . ' then 1 end) as openCount,
                                                     count(case when ticket_status_enum = ' . $ticketPresenter->getTicketStatusEnum('mediation') . ' then 1 end) as mediationCount,
                                                     count(case when ticket_status_enum = ' . $ticketPresenter->getTicketStatusEnum('closed') . ' then 1 end) as closedCount
                                            ')
                                            ->whereHas('sale', function($query) use ($userId) {
                                                $query->where('owner_id', $userId);
                                            });
            if (!empty($data['date'])) {
                $date = FoxUtils::validateDateRange($data["date"]);
                $ticket->whereBetween('created_at', [$date[0] . ' 00:00:00', $date[1] . ' 23:59:59']);
            }
            $ticket     = $ticket->first();
            $totalCount = $ticket->openCount + $ticket->mediationCount + $ticket->closedCount;

            return response()->json([
                                        'total_ticket_open'      => $ticket->openCount,
                                        'total_ticket_mediation' => $ticket->mediationCount,
                                        'total_ticket_closed'    => $ticket->closedCount,
                                        'total_ticket'           => $totalCount,

                                    ]);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao carregar valores totais!'], 400);
        }
    }
}
