<?php

namespace Modules\Chargebacks\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\FoxUtilsService;
use Modules\Core\Services\SaleService;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleContestation;

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

        $userProject = UserProject::with('company')->where([
            ['type_enum', (new UserProject)->present()->getTypeEnum('producer')],
            ['project_id', $this->sale->project_id],
        ])->first();

        $expiration_date = (Carbon::parse($this->expiration_date)->subDay(2));

        $has_expired =  $expiration_date->lessThanOrEqualTo(\Carbon\Carbon::now());
        $descriptionBlackList = (New SaleService())->returnBlacklistBySale($this->sale);
        $result_decode = json_decode($this->data, true);

        $deadline_in_days =  $expiration_date->diffInDays(Carbon::now());


        return [
            'id' => Hashids::encode($this->id) ,
            'transaction_id' => $sale_encode,
            'sale_code' => '#' . $sale_encode,
            'company' => $userProject->company->fantasy_name ??  '',
            'company_limit' => $userProject->company->fantasy_name ? Str::limit($userProject->company->fantasy_name, '20') :  '',
            'user' => $this->user_name ?? '',
            'project' => $project->name ?? '',
            'sale_id' => Hashids::connection('sale_id')->encode($this->sale_id),
            'sale_start_date' => $this->sale->start_date ? with(new Carbon($this->sale->start_date))->format('d/m/Y H:i:s') : '',
            'sale_release_date' => $this->sale->start_date ? with(new Carbon($this->sale->release_date))->format('d/m/Y H:i:s') : '',
            'sale_status' => $this->sale->status ?? '',
            'sale_payment_method' => $this->sale->payment_method,
            'sale_blacklist' => $descriptionBlackList ?? '',
            'sale_has_valid_tracking' => $this->sale->has_valid_tracking,
            'sale_only_digital_products'   => is_null($this->sale->delivery_id),
            'sale_is_chargeback_recovered' =>  $this->sale->sale_status == '1'? $this->sale->is_chargeback_recovered : 0,
            'product' => ($this->sale->plansSales()->count() > 1) ? 'Carrinho' : Str::limit($plan->name ?? '', 25),
            'customer' => $this->customer_name ?? '',
            'transaction_date' => $this->transaction_date ? with(new Carbon($this->transaction_date))->format('d/m/Y') : '',
            'file_date' => $this->file_date ? with(new Carbon($this->file_date))->format('d/m/Y') : '',
            'adjustment_date' => $this->sale->end_date ? with(new Carbon($this->sale->end_date))->format('d/m/Y') : '',
            'request_date' => $this->request_date ? with(new Carbon($this->request_date))->format('d/m/Y') : '',
            'expiration' => $expiration_date ? $expiration_date->format('d/m/Y') : '',
            'has_expired' => $has_expired,
            'expiration_user' => !$has_expired ? ($deadline_in_days == 0 ? "Expira hoje" : ($deadline_in_days > 1 ? $deadline_in_days . ' dias' : $deadline_in_days . ' dia')) : ($this->sale->status == Sale::STATUS_CHARGEBACK ? 'Perdida' : ($this->status == SaleContestation::STATUS_WIN ? 'Ganha' : 'Expirado')),
            'reason' =>  isset($result_decode['Codigo do Motivo de Chargeback']) ? FoxUtils::getnetReasonByCode($result_decode['Codigo do Motivo de Chargeback']) : FoxUtils::getnetReasonByCode($this->reason),
            'observation' =>  $this->observation ?? '',
            'is_contested' =>  $this->is_contested ?? '',
            'amount' => isset($this->sale->original_total_paid_value) ? 'R$ ' . number_format(intval($this->sale->original_total_paid_value) / 100, 2, ',', '.') :
                ''
            ,
            'files' => $this->files ?  SaleContestationFileResource::collection($this->files) : '',
            'has_files' => $this->files->count() ? true:false,
            'is_file_user_completed' => $this->file_user_completed,

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
