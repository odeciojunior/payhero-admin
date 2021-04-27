<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\WithdrawalPresenter;

/**
 * @property integer $id
 * @property integer $customer_id
 * @property int $value
 * @property int $status
 * @property mixed $bank_account
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Customer $customer
 * @method WithdrawalPresenter present()
 */
class CustomerWithdrawal extends Model
{
    use PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = WithdrawalPresenter::class;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['customer_id', 'value', 'status', 'bank_account', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
