<?php

namespace Modules\Tickets\Transformers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketAttachmentResource extends JsonResource
{
    public function toArray($request)
    {
        $filename = pathinfo($this->file, PATHINFO_BASENAME);
        $expiration = now()->addMinutes(config('session.lifetime'));
        $url = Storage::disk('s3_documents')->temporaryUrl('uploads/private/tickets/attachments/' . $filename, $expiration);

        return [
            'id' => Hashids::encode($this->id),
            'content' => basename($this->filename),
            'type' => 'file',
            'created_at' => Carbon::parse($this->created_at)->format('d/m \à\s H\hi'),
            'filename' => $this->filename ?? null,
            'link' => $url,
            'from' => $this->type_enum,
        ];
    }
}
