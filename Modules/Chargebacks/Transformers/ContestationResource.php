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
use Modules\Sales\Transformers\SalesResource;
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

        $userProject = UserProject::with('company')->where([
            ['type_enum', (new UserProject)->present()->getTypeEnum('producer')],
            ['project_id', $this->sale->project_id],
        ])->first();

        $descriptionBlackList = (New SaleService())->returnBlacklistBySale($this->sale);
        $result_decode = json_decode($this->data, true);

        return [
            'id' => Hashids::encode($this->id),
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
            'expiration' => $this->expiration_date ? with(new Carbon($this->expiration_date))->format('d/m/Y') : '',
            'has_expired' => $this->expiration_date  ? \Carbon\Carbon::parse($this->expiration_date)->lessThan(\Carbon\Carbon::now()) : false,
            'expiration_user' => $this->expiration_date ? ((Carbon::parse($this->expiration_date))->subDay(3))->format('d/m/Y') : '',
            'reason' =>  isset($result_decode['Codigo do Motivo de Chargeback']) ? $result_decode['Codigo do Motivo de Chargeback'] . ' - ' . $this->returnReasonDescription($result_decode['Codigo do Motivo de Chargeback'], $result_decode['Motivo do Chargeback']) : '',
            'observation' =>  $this->observation ?? '',
            'is_contested' =>  $this->is_contested ?? '',
            'amount' => isset($this->sale->original_total_paid_value) ? 'R$ ' . number_format(intval($this->sale->original_total_paid_value) / 100, 2, ',', '.') :
                ''
            ,
            'files' => $this->files ?  SaleContestationFileResource::collection($this->files) : ''

        ];
    }

    public function returnReasonDescription(string $code, $reason = null)
    {

        switch($code) {
            case '4837':
            case '74':
            case '103':
            case '104':
            case '4540':
            case '4755':
            return 'Portador não reconhece a transação';
            case '4840':
            case '57':
            return 'Múltiplas transações fraudulentas';
            case '4860':
            case '75':
            case '136':
            case '137':
            case '85':
            case '4513':
            return 'Cancelamento / crédito não processado';
            case '4855':
            case '79':
            case '131':
            case '30':
            case '4554':
            return 'Mercadoria / serviços não prestados';
            case '4841':
            case '132':
            case '41':
            case '4544':
            return 'Cancelamento de transações recorrentes';
            case '133':
            case '134':
            case '53':
            case '4553':
            return 'Mercadoria falsificada / defeituosa ou não conforme com o descrito';
            case '4853':
            case '135':
            return 'Desacordo comercial (no geral)';
            case '4859':
                return 'Valor adicional cobrado por um serviço prestado ou NO SHOW';
            case '4834':
            case '73':
            case '1261':
            case '82':
            case '4512':
            return 'Duplicidade da transação';
            case '4831':
            case '1262':
            case '86':
            case '4515':
            return 'Pagamentos por outros meios';
            case '123':
            case '4530':
            return 'Moeda incorreta';
            case '124':
            case '4507':
            case '4523':
            return 'Valor da transação ou número de conta incorreta ou inexistente';
            case '4527':
                return 'Falta de impressão';
            case '4534':
                return 'Múltiplos comprovantes';
            case '80':
            case '4753':
            return 'Erro / divergência de processamento';
            case '125':
            case '77':
            return 'Valor incorreto';
            case '4850':
                return 'Transação fraudulenta/sem autorização';
            default:
                return isset($reason) ? str_replace("?", "Ã", $reason) : '';

        }

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
