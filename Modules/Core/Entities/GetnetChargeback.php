<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $getnet_chargeback_detail_id
 * @property integer $sale_id
 * @property int $company_id
 * @property int $project_id
 * @property int $user_id
 * @property string $transaction_date
 * @property string $installment_date
 * @property string $adjustment_date
 * @property float $adjustment_amount
 * @property float $chargeback_amount
 * @property mixed $body
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Company $company
 * @property GetnetChargebackDetail $getnetChargebackDetail
 * @property Project $project
 * @property Sale $sale
 * @property User $user
 */
class GetnetChargeback extends Model
{

    /**
     * @var array
     */
    protected $fillable = ['getnet_chargeback_detail_id', 'sale_id', 'company_id', 'project_id', 'user_id', 'transaction_date', 'installment_date', 'adjustment_date', 'adjustment_amount', 'chargeback_amount', 'body', 'created_at', 'updated_at', 'deleted_at'];

}
