<?php

namespace Modules\Core\Entities;


use Illuminate\Database\Eloquent\Model;


/**
 * @property integer $id
 * @property string $filters
 * @property mixed $body
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property GetnetChargeback[] $getnetChargebacks
 */
class GetnetChargebackDetail extends Model
{

    /**
     * @var array
     */
    protected $fillable = ['filters', 'body', 'created_at', 'updated_at', 'deleted_at'];


}
