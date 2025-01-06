<?php

namespace Modules\Tickets\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketAttachment;
use Modules\Core\Entities\TicketMessage;
use Modules\Core\Entities\User;
use Modules\Core\Events\Sac\TicketMessageEvent;
use Modules\Core\Services\AttendanceService;
use Modules\Core\Services\FoxUtils;
use Modules\Tickets\Transformers\TicketAttachmentResource;
use Modules\Tickets\Transformers\TicketMessageResource;
use Modules\Tickets\Transformers\TicketResource;
use Modules\Tickets\Transformers\TicketShowResource;
use Vinkla\Hashids\Facades\Hashids;

class TicketsApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = (object)$request->all();

            $userId = auth()->user()->getAccountOwnerId();

            $ticketsQuery = Ticket::select([
                'tickets.id',
                'tickets.subject',
                DB::raw("ifnull((select m.message from ticket_messages as m where m.ticket_id = tickets.id order by id desc limit 1), tickets.description) as description"),
                'tickets.ticket_status_enum',
                'tickets.last_message_type_enum',
                'customers.name as customer_name',
                'sales.id as sale_id'
            ])->withCount([
                'messages as admin_answers' => function ($query) {
                    $query->where('type_enum', TicketMessage::TYPE_FROM_ADMIN);
                }
            ])->join('sales', 'tickets.sale_id', '=', 'sales.id')
                ->join('customers', 'sales.customer_id', '=', 'customers.id');

            if($data->project)
            {
                if(str_starts_with($data->project,'TOKEN')){
                    $ticketsQuery->join('api_tokens as api', 'sales.api_token_id','=','api.id')
                    ->where('api.company_id', hashids_decode($data->company))
                    ->where("sales.api_token_id", hashids_decode(str_replace('TOKEN-','',$data->project)));
                }else{
                    $ticketsQuery->join('checkout_configs', 'sales.project_id','=','checkout_configs.project_id')
                    ->where('checkout_configs.company_id', hashids_decode($data->company))
                    ->where("sales.project_id", hashids_decode(str_replace('TOKEN-','',$data->project)));
                }
            }else{

                if (empty($data) || !isset($data->company)) {
                    return response()->json(["message" => "Empresa não encontrada."], 404);
                }
                
                $ticketsQuery->leftJoin('api_tokens as api', 'sales.api_token_id','=','api.id')
                ->leftJoin('checkout_configs', 'sales.project_id','=','checkout_configs.project_id')
                ->where(function($query) use($data){
                    $query->where(function($qr2) use($data){
                        $qr2->whereNotNull("sales.api_token_id")
                        ->where('api.company_id', hashids_decode($data->company));
                    })->orWhere(function($qr2) use($data){
                        $qr2->whereNotNull("sales.project_id")
                        ->where('checkout_configs.company_id', hashids_decode($data->company));
                    });
                });
            }

            if ($data->plan) {
                $ticketsQuery->whereExists(function ($query) use ($data) {
                    $query
                        ->select(DB::raw(1))
                        ->from("plans_sales")
                        ->where("plans_sales.sale_id", DB::raw("sales.id"))
                        ->where("plans_sales.plan_id", hashids_decode($data->plan));
                });
            }

            if (!empty($data->transaction)) {
                preg_match_all("/[0-9A-Za-z]+/", $data->transaction, $matches);
                $ids = array_map(function ($item) {
                    return is_numeric($item) ? $item : hashids_decode($item, "sale_id");
                }, current($matches));

                $ticketsQuery->whereIn("sale_id", $ids);
            }

            if ($data->document) {
                $document = preg_replace("/[^0-9]/", "", $data->document);
                $ticketsQuery->where("customers.document", $document);
            }

            if ($data->name) {
                $ticketsQuery->where("customers.name", "like", "%$data->name%");
            }

            if ($data->answered) {
                if ($data->answered === "last-answer-admin") {
                    $ticketsQuery->where("last_message_type_enum", TicketMessage::TYPE_FROM_ADMIN);
                } elseif ($data->answered === "last-answer-customer") {
                    $ticketsQuery
                        ->whereHas("messages")
                        ->where("last_message_type_enum", TicketMessage::TYPE_FROM_CUSTOMER);
                } else {
                    $ticketsQuery->doesntHave("messages");
                }
            }

            if ($data->period && !$data->transaction) {
                $dateRange = FoxUtils::validateDateRange($data->period);
                $ticketsQuery->whereBetween("tickets.created_at", [
                    $dateRange[0] . " 00:00:00",
                    $dateRange[1] . " 23:59:59",
                ]);
            }

            if ($data->status) {
                $ticketsQuery->where("tickets.ticket_status_enum", $data->status);
            }

            if ($data->nameOrDocument) {
                $value = $data->nameOrDocument;
                $ticketsQuery->where(function ($query) use ($value) {
                    $query->where("customers.name", "like", "%$value%")->orWhere("customers.document", $value);
                });
            }

            $tickets = $ticketsQuery
                ->where('sales.owner_id', $userId)
                ->orderByDesc('tickets.id')
                ->paginate(5);
            return TicketResource::collection($tickets);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Erro ao carregar chamados"], 400);
        }
    }

    public function show($id)
    {
        try {
            $ticketId = current(Hashids::decode($id ?? ""));

            if (!empty($ticketId)) {
                $ticket = Ticket::select([
                    "tickets.id",
                    "tickets.sale_id",
                    "tickets.description",
                    "tickets.ticket_category_enum",
                    "tickets.ticket_status_enum",
                    "tickets.created_at",
                    "projects.name as project_name",
                    "customers.name as customer_name",
                ])
                    ->with(["messages", "attachments"])
                    ->join("sales", "tickets.sale_id", "=", "sales.id")
                    ->join("projects", "sales.project_id", "=", "projects.id")
                    ->join("customers", "sales.customer_id", "=", "customers.id")
                    ->find($ticketId);

                if (empty($ticket)) {
                    return response()->json(["message" => "Chamado não encontrado!"], 404);
                }

                return new TicketShowResource($ticket);
            } else {
                return response()->json(["message" => "Chamado não encontrado!"], 404);
            }
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao carregar chamado"], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            $data = $request->all();

            $ticketId = current(Hashids::decode($data["ticket_id"] ?? ""));
            $ticket = Ticket::find($ticketId);

            if (!empty($ticket)) {
                $response = [];

                if (!empty($data["message"])) {
                    $messageEmail = explode(" ", $data["message"]);
                    foreach ($messageEmail as $key => $value) {
                        $position = stripos($value, "@");
                        if ($position !== false) {
                            if (FoxUtils::validateEmail($value)) {
                                return response()->json(["message" => "Não é permitido enviar email na mensagem"], 400);
                            }
                        }
                    }

                    $lastAdminMessage = TicketMessage::where("ticket_id", $ticket->id)
                        ->where("type_enum", TicketMessage::TYPE_FROM_ADMIN)
                        ->latest("id")
                        ->first();

                    $message = TicketMessage::create([
                        "ticket_id" => $ticket->id,
                        "message" => $data["message"],
                        "type_enum" => TicketMessage::TYPE_FROM_ADMIN,
                    ]);

                    $response[] = new TicketMessageResource($message);

                    $attendanceService = new AttendanceService();
                    $averageResponseTime = $attendanceService->getTicketAverageResponseTime($ticket);
                    $ticket->update(["average_response_time" => $averageResponseTime]);

                    event(new TicketMessageEvent($message, $lastAdminMessage));
                }

                if (!empty($data["attachments"])) {
                    foreach ($data["attachments"] as $file) {
                        $urlPath = "uploads/private/tickets/attachments/";
                        $filename = Str::uuid() . "." . $file->getClientOriginalExtension();
                        Storage::disk("s3_documents")->put($urlPath . $filename, File::get($file->getRealPath()));
                        $url = Storage::disk("s3_documents")->url($urlPath . $filename);
                        $attachment = TicketAttachment::create([
                            "ticket_id" => $ticket->id,
                            "file" => $url,
                            "filename" => $file->getClientOriginalName(),
                            "type_enum" => TicketAttachment::TYPE_FROM_ADMIN,
                        ]);

                        $response[] = new TicketAttachmentResource($attachment);
                    }
                }

                return response()->json($response);
            } else {
                return response()->json(["message" => "Chamado não encontrado"], 404);
            }
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao enviar mensagem"], 500);
        }
    }

    public function getTotalValues(Request $request)
    {
        try {
            $ticketsModel = new Ticket();
            $data = $request->all();
            $userId = auth()->user()->getAccountOwnerId();

            $ticketPresenter = $ticketsModel->present();
            $ticket = $ticketsModel->selectRaw('count(case when ticket_status_enum = ' . $ticketPresenter->getTicketStatusEnum('open') . ' then 1 end) as openCount,
                                                     count(case when ticket_status_enum = ' . $ticketPresenter->getTicketStatusEnum('mediation') . ' then 1 end) as mediationCount,
                                                     count(case when ticket_status_enum = ' . $ticketPresenter->getTicketStatusEnum('closed') . ' then 1 end) as closedCount
                                            ')
                ->join('sales', 'tickets.sale_id','=','sales.id')
                ->join('checkout_configs', 'sales.project_id','=','checkout_configs.project_id')
                ->where('sales.owner_id', $userId)
                ->where('checkout_configs.company_id', hashids_decode($data['company_id']));
                if (!empty($data['project'])) {
                    $projectId = hashids_decode($data['project']);
                    $ticket->where('sales.project_id', $projectId);
                }
            $ticket = $ticket->first();
            $totalCount = $ticket->openCount + $ticket->mediationCount + $ticket->closedCount;

            return response()->json([
                "open" => $ticket->openCount,
                "mediation" => $ticket->mediationCount,
                "closed" => $ticket->closedCount,
                "total" => $totalCount,
            ]);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Erro ao carregar valores totais!"], 400);
        }
    }

    public function getFile($id)
    {
        try {
            $attachmentId = current(Hashids::decode($id));
            $attachment = TicketAttachment::find($attachmentId);

            $filename = pathinfo($attachment->file, PATHINFO_BASENAME);
            $expiration = now()->addMinutes(config("session.lifetime"));
            $url = Storage::disk("s3_documents")->temporaryUrl(
                "uploads/private/tickets/attachments/" . $filename,
                $expiration
            );

            return response()->json(["url" => $url]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Não foi possível acessar o arquivo solicitado."], 400);
        }
    }
}
