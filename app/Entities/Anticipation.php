<?php

namespace App\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anticipation extends Model
{
    use FoxModelTrait;
    /**
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'value',
        'tax',
        'percentage_tax',
        'release_money_days',
        'company_id',
    ];

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Entities\Companies');
    }
}
