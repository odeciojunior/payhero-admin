<?php

namespace Modules\Tickets\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketAttachment;
use Modules\Core\Entities\TicketMessage;
use Modules\Core\Events\TicketMessageEvent;
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
            $data = $request->all();
            $userId = auth()->user()->account_owner_id;
            $tickets = $ticketsModel->with([
                'messages',
                'customer',
                'sale',
            ])->whereHas('sale', function ($query) use ($userId) {
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
                $tickets->whereHas('customer', function ($query) use ($customerName) {
                    $query->where('name', 'LIKE', '%' . $customerName . '%');
                });
            }
            if (!empty($data['cpf'])) {
                $document = preg_replace("/[^0-9]/", "", $data['cpf']);
                $tickets->whereHas('customer', function ($query) use ($document) {
                    $query->where('document', $document);
                });
            }
            if (!empty($data['ticket_id'])) {
                $ticketId = current(Hashids::decode($data['ticket_id'] ?? ''));
                $tickets->where('id', $ticketId);
            }
            if (!empty($data['answered'])) {
                if ($data['answered'] == 'last-answer-admin') {
                    $tickets->where('last_message_type_enum', $ticketsModel->present()->getLastMessageType('from_admin'));
                } else if ($data['answered'] == 'last-answer-customer') {
                    $tickets->whereHas('messages')
                        ->where('last_message_type_enum', $ticketsModel->present()->getLastMessageType('from_customer'));
                } else {
                    $tickets->doesntHave('messages');
                }
            }
            $tickets = $tickets->orderByDesc('id')
                ->paginate(5);

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
                    'sale.project',
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

    public function sendMessage(Request $request)
    {
        try {
            $ticketModel = new Ticket();
            $ticketMessageModel = new TicketMessage();

            $data = $request->all();

            $ticketId = current(Hashids::decode($data['ticket_id'] ?? ''));
            $ticket = $ticketModel->find($ticketId);

            if (!empty($ticket)) {

                if (!empty($data['message'])) {

                    if(strlen($data['message']) < 10) {
                        return response()->json(['message' => 'A mensagem informada é muito curta!'], 400);
                    }

                    $messageEmail = explode(' ', $data['message']);
                    foreach ($messageEmail as $key => $value) {
                        $position = stripos($value, '@');
                        if($position !== false) {
                            if(FoxUtils::validateEmail($value)) {
                                return response()->json(['message' => 'Não é permitido enviar email na mensagem'], 400);
                            }
                        }
                    }

                    // $messagePhone = $data['message'];
                    // $string = '';
                    // for($i=0; $i<strlen($messagePhone); $i++){
                    //     $string = $messagePhone[$i];
                    //     if(is_numeric($string)) {
                    //         $phone = substr($messagePhone, $i, 18);
                    //         $phone = preg_replace("/[^0-9]/", "", $phone);
                    //         if(in_array(strlen($phone), [10,11,13])) {
                    //             return response()->json(['message' => 'Não é permitido enviar telefone na mensagem'], 400);
                    //         }
                    //     }
                    // }

                    $lastAdminMessage = $ticketMessageModel->where('ticket_id', $ticket->id)
                        ->where('type_enum', $ticketMessageModel->present()->getType('from_admin'))
                        ->latest('id')
                        ->first();
                    $message = $ticketMessageModel->create([
                        'ticket_id' => $ticket->id,
                        'message' => $data['message'],
                        'type_enum' => $ticketMessageModel->present()->getType('from_admin'),
                    ]);

                    event(new TicketMessageEvent($message, $lastAdminMessage));

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
            $ticketsModel = new Ticket();
            $data = $request->all();
            $userId = auth()->user()->account_owner_id;
            $ticketPresenter = $ticketsModel->present();
            $ticket = $ticketsModel->selectRaw('count(case when ticket_status_enum = ' . $ticketPresenter->getTicketStatusEnum('open') . ' then 1 end) as openCount,
                                                     count(case when ticket_status_enum = ' . $ticketPresenter->getTicketStatusEnum('mediation') . ' then 1 end) as mediationCount,
                                                     count(case when ticket_status_enum = ' . $ticketPresenter->getTicketStatusEnum('closed') . ' then 1 end) as closedCount
                                            ')
                ->whereHas('sale', function ($query) use ($userId) {
                    $query->where('owner_id', $userId);
                });
            if (!empty($data['date'])) {
                $date = FoxUtils::validateDateRange($data["date"]);
                $ticket->whereBetween('created_at', [$date[0] . ' 00:00:00', $date[1] . ' 23:59:59']);
            }
            $ticket = $ticket->first();
            $totalCount = $ticket->openCount + $ticket->mediationCount + $ticket->closedCount;

            return response()->json([
                'total_ticket_open' => $ticket->openCount,
                'total_ticket_mediation' => $ticket->mediationCount,
                'total_ticket_closed' => $ticket->closedCount,
                'total_ticket' => $totalCount,

            ]);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao carregar valores totais!'], 400);
        }
    }

    public function getFile($id)
    {
        try {
            $attachmentId = current(Hashids::decode($id));
            $attachment = TicketAttachment::find($attachmentId);

            $filename = pathinfo($attachment->file, PATHINFO_BASENAME);
            $expiration = now()->addMinutes(config('session.lifetime'));
            $url = Storage::cloud()->temporaryUrl('uploads/private/tickets/attachments/'. $filename, $expiration);

            return redirect($url);

        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro obter anexo'], 400);
        }
    }
}
