<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\WithdrawalPresenter;

/**
 * @property integer $id
 * @property int $company_id
 * @property string $value
 * @property string $release_date
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $bank
 * @property string $agency
 * @property string $agency_digit
 * @property string $account
 * @property string $account_digit
 * @property Company $company
 */
class Withdrawal extends Model
{
    use PresentableTrait;

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
    protected $fillable = [
        'company_id', 
        'value', 
        'release_date', 
        'status', 
        'created_at', 
        'updated_at', 
        'bank', 
        'agency', 
        'agency_digit', 
        'account', 
        'account_digit'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('Modules\Core\Entities\Company');
    }
}
