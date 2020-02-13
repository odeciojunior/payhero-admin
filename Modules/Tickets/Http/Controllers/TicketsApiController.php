<?php

namespace Modules\Tickets\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketMessage;
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

            $data = $request->all();

            if (!empty($customerId)) {

                $tickets = $ticketsModel->with([
                    'messages',
                ]);

                if (!empty($data['status'])) {
                    $tickets->where('ticket_status_enum', $ticketsModel
                        ->present()
                        ->getTicketStatusEnum($data['status']));
                }

                $tickets = $tickets->paginate(10);

                return TicketResource::collection($tickets);

            } else {
                return response()->json(['message' => 'Cliente não encontrado!'], 400);
            }

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
                    'attachments'
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
                    'ticket_status_enum' => $ticket->present()->getTicketStatusEnum($data['status'])
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

            $tickeMessageModel = new TicketMessage();

            $data = $request->all();

            $ticketId = current(Hashids::decode($data['ticket_id'] ?? ''));

            if (!empty($ticketId)) {

                if (!empty($data['message'])) {
                    $message = $tickeMessageModel->create([
                        'ticket_id' => $ticketId,
                        'message' => $data['message'],
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
}
