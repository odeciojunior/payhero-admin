<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Presenters\CustomerBankAccountPresenter;

/**
 * @property integer $id
 * @property integer $customer_id
 * @property string $holder_name
 * @property integer $account_type
 * @property integer $bank
 * @property string $agency
 * @property string $account
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @method CustomerBankAccountPresenter present()
 */
class CustomerBankAccount extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = CustomerBankAccountPresenter::class;

    /**
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'holder_name',
        'holder_document',
        'account_type',
        'bank',
        'agency',
        'account',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
