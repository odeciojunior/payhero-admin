<?php

namespace Modules\Chargebacks\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Core\Entities\SaleWhiteBlackListResult;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\FoxUtilsService;
use Modules\Core\Services\SaleService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ContestationResource
 * @package Modules\Companies\Transformers
 */
class SaleContestationFileResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array,
     * @throws Exception
     */
    public function toArray($request)
    {
        $expiration = now()->addMinutes(config("session.lifetime"));
        $url = Storage::disk("s3_documents")->temporaryUrl($this->file, $expiration);

        return [
            "id" => Hashids::encode($this->id),
            "sale_hash" => Hashids::connection("sale_id")->encode($this->contestation->sale_id),
            "user_id" => Hashids::encode($this->user_id),
            "contestation_sale_id" => Hashids::encode($this->contestation_sale_id),
            "type" => $this->type,
            "type_str" => $this->typeStr($this->type),
            "file" => $url,
            "remove_route" => route("contestations.removeContestationFiles", Hashids::encode($this->id)),
            "created_at" => with(new Carbon($this->created_at))->format("d/m/Y"),
        ];
    }

    private function typeStr($type)
    {
        switch ($type) {
            case "NOTA_FISCAL":
                return "Nota fiscal";
            case "POLITICA_VENDA":
                return "Politica de venda";
            case "ENTREGA":
                return "Entrega";
            case "INFO_ACORDO":
                return "Informação do acordo";
            default:
                return "Outros";
        }
    }
}
