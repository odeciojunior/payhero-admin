<?php

namespace App\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $company_id
 * @property string $value
 * @property string $release_date
 * @property string $account_information
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property Company $company
 */
class Withdrawal extends Model
{
    use FoxModelTrait;
    /**
     * The "type" of the auto-incrementing ID.
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
        'bank',
        'agency',
        'agency_digit',
        'account',
        'account_digit',
        'status',
        'created_at',
        'updated_at',
    ];
    /**
     * @var array
     */
    private $enum = [
        'status' => [
            1 => 'pending',
            2 => 'approved',
            3 => 'transfered',
            4 => 'refused',
        ],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Entities\Company');
    }
}


