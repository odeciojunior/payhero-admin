<?php

namespace Modules\Tickets\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\TicketAttachment;
use Modules\Core\Entities\TicketMessage;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\DigitalOceanFileService;
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

            $ticketsModel = new Ticket();

            $data = $request->all();

            $customerId = auth()->id();

            if (!empty($customerId)) {

                $tickets = $ticketsModel->with([
                    'messages',
                ])->where('customer_id', $customerId);

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

    public function create(Request $request)
    {
        try {
            $salesModel = new Sale();
            $companyService = new CompanyService();

            $data = $request->all();

            if(!empty($data['order'])) {

                $customerId = auth()->id();
                $saleId = current(Hashids::connection('sale_id')->decode($data['order'] ?? ''));

                $sale = $salesModel->with([
                    'productsPlansSale.product',
                    'project.usersProjects.company'
                ])->where('customer_id', $customerId)
                    ->where('id', $saleId)
                    ->first();

                $products = [];
                $items = 0;

                //obter a quantidade de cada produto em uma venda
                foreach ($sale->productsPlansSale as $productPlanSale) {

                    $items += $productPlanSale->amount;

                    $product = $productPlanSale->product;

                    $products[] = [
                        'id' => Hashids::encode($product->id),
                        'name' => $product->name,
                        'description' => $product->description,
                        'amount' => $productPlanSale->amount,
                        'digital' => false,
                    ];
                }

                $company = $sale->project->usersProjects->first()->company;

                $currency = $companyService->getCurrency($company, true);

                $sale = [
                    'id' => Hashids::connection('sale_id')->encode($sale->id),
                    'company' => $company->fantasy_name ?? '',
                    'items' => $items,
                    'amount' => $currency . ' ' . number_format($sale->total_paid_value, 2, ',', '.'),
                    'products' => $products,
                ];

                return response()->json($sale);
            } else {
                return response()->json(["message" => "Compra não encontrada"], 400);
            }
        } catch (Exception $e) {
            return response()->json(["message" => "Erro ao buscar compra"], 400);
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

    public function store(Request $request)
    {
        try {

            $ticketsModel = new Ticket();
            $ticketsAttachmentsModel = new TicketAttachment();

            $data = $request->all();

            $saleId = current(Hashids::connection('sale_id')->decode($data['order'] ?? ''));

            if (!empty($saleId) && !empty($data['subject']) && !empty($data['description']) && !empty($data['category'])) {

                $customertId = auth()->id();

                $ticket = $ticketsModel->create([
                    'sale_id' => $saleId,
                    'customer_id' => $customertId,
                    'subject' => $data['subject'],
                    'description' => $data['description'],
                    'ticket_category_enum' => $data['category'],
                    'ticket_status_enum' => $ticketsModel->present()->getTicketStatusEnum('open'),
                ]);

                if (!empty($ticket)) {
                    if ($request->hasFile('attachments')) {
                        $digitalOceanFileService = app(DigitalOceanFileService::class);
                        $files = $request->file('attachments');
                        foreach ($files as $file) {
                            $digitalOceanPath = $digitalOceanFileService->uploadFile('uploads/ticket/' . Hashids::encode($ticket->id) . '/private/attachments', $file, null, null, 'private');

                            $ticketsAttachmentsModel->create([
                                'ticket_id' => $ticket->id,
                                'file' => $digitalOceanPath,
                            ]);
                        }
                    }
                } else {
                    return response()->json(['message' => 'Erro ao criar chamado'], 400);
                }

                return response()->json(['message' => 'Chamado criado com successo']);

            } else {
                return response()->json(['message' => 'Dados inválidos!'], 400);
            }

        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao criar chamado'], 400);
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
                        'message' => $data['message']
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

    public function upload(Request $request)
    {
        try {
            $ticketsAttachmentsModel = new TicketAttachment();

            $data = $request->all();

            $ticketId = current(Hashids::decode($data['ticket_id'] ?? ''));

            if (!empty($ticketId)) {

                if ($request->hasFile('attachments')) {
                    $digitalOceanFileService = app(DigitalOceanFileService::class);
                    $files = $request->file('attachments');
                    $attachments = collect();
                    foreach ($files as $file) {
                        $digitalOceanPath = $digitalOceanFileService->uploadFile('uploads/ticket/' . Hashids::encode($ticketId) . '/private/attachments', $file, null, null, 'private');

                        $attachment = $ticketsAttachmentsModel->create([
                            'ticket_id' => $ticketId,
                            'file' => $digitalOceanPath,
                        ]);

                        $attachments->push($attachment);
                    }

                    return TicketAttachmentResource::collection($attachments);

                } else {
                    return response()->json(['message' => 'Anexos inválidos'], 400);
                }
            } else {
                return response()->json(['message' => 'Chamado não encontrado'], 400);
            }

        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao salvar anexo'], 400);
        }
    }

    public function deleteFile(Request $request)
    {
        try {
            $ticketsAttachmentsModel = new TicketAttachment();

            $data = $request->all();

            $attachmentId = current(Hashids::decode($data['attachment_id'] ?? ''));

            if (!empty($attachmentId)) {

                $digitalOceanFileService = app(DigitalOceanFileService::class);

                $attachment = $ticketsAttachmentsModel->find($attachmentId);

                if (!empty($attachmentId)) {

                    $digitalOceanFileService->deleteFile($attachment->file);

                    $attachment->delete();

                    return response()->json(['message' => 'Anexo excluído com sucesso!']);
                } else {
                    return response()->json(['message' => 'Anexo não encontrado'], 400);
                }

            } else {
                return response()->json(['message' => 'Anexo não encontrado'], 400);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao salvar anexo'], 400);
        }
    }
}
