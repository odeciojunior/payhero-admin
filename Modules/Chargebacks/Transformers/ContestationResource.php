<?php

namespace Modules\Chargebacks\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
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
class ContestationResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array,
     * @throws Exception
     */
    public function toArray($request)
    {

        $plansSale = $this->sale->plansSales()->first();
        $plan = $plansSale ? $plansSale->plan : null;
        $project = $plan ? $plan->project : null;

        $sale_encode = Hashids::connection('sale_id')->encode($this->sale_id);
        $data_decode = json_decode($this->data, true);

        $userProject = UserProject::with('company')->where([
            ['type_enum', (new UserProject)->present()->getTypeEnum('producer')],
            ['project_id', $this->sale->project_id],
        ])->first();

        $descriptionBlackList = (New SaleService())->returnBlacklistBySale($this->sale);
        $result_decode = json_decode($this->data, true);

        $expiration = isset($data_decode['Data do Retorno']) ? \Carbon\Carbon::createFromFormat('dmY', $data_decode['Data do Retorno'])->format('d/m/Y') : '';

        return [
            'id' => Hashids::encode($this->id),
            'transaction_id' => $sale_encode,
            'sale_code' => '#' . $sale_encode,
            'company' => $userProject->company->fantasy_name ?? '',
            'user' => $this->user_name ?? '',
            'project' => $project->name ?? '',
            'sale_id' => $this->sale_id,
            'sale_status' => $this->sale->status ?? '',
            'sale_blacklist' => $descriptionBlackList ?? '',
            'sale_has_valid_tracking' => $this->sale->has_valid_tracking,
            'sale_only_digital_products'   => is_null($this->sale->delivery_id),
            'sale_is_chargeback_recovered' =>  $this->sale->sale_status == '1'? $this->sale->is_chargeback_recovered : 0,
            'product' => ($this->sale->plansSales()->count() > 1) ? 'Carrinho' : Str::limit($plan->name ?? '', 25),
            'customer' => $this->customer_name ?? '',
            'transaction_date' => $this->transaction_date ? with(new Carbon($this->transaction_date))->format('d/m/Y') : '',
            'file_date' => $this->file_date ? with(new Carbon($this->file_date))->format('d/m/Y') : '',
            'adjustment_date' => $this->sale->end_date ? with(new Carbon($this->sale->end_date))->format('d/m/Y') : '',
            'expiration' => $expiration,
            'reason' =>  isset($result_decode['Motivo do Chargeback']) ? str_replace("?", "Ã", $result_decode['Motivo do Chargeback']) : '',
            'observation' =>  $this->observation ?? '',
            'is_contested' =>  $this->is_contested ?? '',
            'amount' => isset($this->sale->original_total_paid_value) ? 'R$ ' . number_format(intval($this->sale->original_total_paid_value) / 100, 2, ',', '.') :
                ''
            ,

        ];
    }

    /**
     * @param mixed $offset
     * @return bool
     * @see https://github.com/laravel/framework/issues/29916
     */
    public function offsetExists($offset)
    {
        // array_key_exists($offset, $this->resource) is deprecated php7.4;
        return property_exists($this->resource, $offset);
    }
}
