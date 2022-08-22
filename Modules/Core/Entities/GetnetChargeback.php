<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Modules\Core\Entities\GetnetChargeback
 *
 * @property int $id
 * @property int $sale_id
 * @property int $company_id
 * @property int $project_id
 * @property int $user_id
 * @property string|null $transaction_date
 * @property string|null $installment_date
 * @property string|null $adjustment_date
 * @property string|null $adjustment_amount
 * @property int|null $amount
 * @property int $is_debited
 * @property int $tax
 * @property string|null $debited_at
 * @property mixed|null $body
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Company $company
 * @property-read Project $project
 * @property-read Sale $sale
 * @property-read User $user
 * @method static Builder|GetnetChargeback newModelQuery()
 * @method static Builder|GetnetChargeback newQuery()
 * @method static Builder|GetnetChargeback query()
 * @method static Builder|GetnetChargeback whereAdjustmentAmount($value)
 * @method static Builder|GetnetChargeback whereAdjustmentDate($value)
 * @method static Builder|GetnetChargeback whereAmount($value)
 * @method static Builder|GetnetChargeback whereBody($value)
 * @method static Builder|GetnetChargeback whereCompanyId($value)
 * @method static Builder|GetnetChargeback whereCreatedAt($value)
 * @method static Builder|GetnetChargeback whereDebitedAt($value)
 * @method static Builder|GetnetChargeback whereDeletedAt($value)
 * @method static Builder|GetnetChargeback whereId($value)
 * @method static Builder|GetnetChargeback whereInstallmentDate($value)
 * @method static Builder|GetnetChargeback whereIsDebited($value)
 * @method static Builder|GetnetChargeback whereProjectId($value)
 * @method static Builder|GetnetChargeback whereSaleId($value)
 * @method static Builder|GetnetChargeback whereTax($value)
 * @method static Builder|GetnetChargeback whereTransactionDate($value)
 * @method static Builder|GetnetChargeback whereUpdatedAt($value)
 * @method static Builder|GetnetChargeback whereUserId($value)
 */
class GetnetChargeback extends Model
{
    protected $fillable = [
        "sale_id",
        "company_id",
        "project_id",
        "user_id",
        "transaction_date",
        "installment_date",
        "adjustment_date",
        "amount",
        "body",
        "tax",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getnetChargebackDetails(): HasMany
    {
        return $this->hasMany(GetnetChargebackDetail::class, "getnet_chargeback_id");
    }
}
