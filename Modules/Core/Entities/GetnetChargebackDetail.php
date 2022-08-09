<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Core\Entities\GetnetChargebackDetail
 *
 * @property integer $id
 * @property string $filters
 * @property mixed $body
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property GetnetChargeback[] $getnetChargebacks
 * @property int|null $getnet_chargeback_id
 * @method static Builder|GetnetChargebackDetail newModelQuery()
 * @method static Builder|GetnetChargebackDetail newQuery()
 * @method static Builder|GetnetChargebackDetail query()
 * @method static Builder|GetnetChargebackDetail whereBody($value)
 * @method static Builder|GetnetChargebackDetail whereCreatedAt($value)
 * @method static Builder|GetnetChargebackDetail whereDeletedAt($value)
 * @method static Builder|GetnetChargebackDetail whereFilters($value)
 * @method static Builder|GetnetChargebackDetail whereGetnetChargebackId($value)
 * @method static Builder|GetnetChargebackDetail whereId($value)
 * @method static Builder|GetnetChargebackDetail whereUpdatedAt($value)
 */
class GetnetChargebackDetail extends Model
{
    protected $fillable = ["filters", "getnet_chargeback_id", "body", "created_at", "updated_at", "deleted_at"];

    public function getnetChageback()
    {
        return $this->belongsTo(GetnetChargeback::class);
    }
}
