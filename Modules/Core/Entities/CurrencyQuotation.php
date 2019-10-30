<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\CurrencyQuotationPresenter;

/**
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Product[] $products
 */
class CurrencyQuotation extends Model
{
    use FoxModelTrait, PresentableTrait;
    /**
     * @var string
     */
    protected $presenter = CurrencyQuotationPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $appends = ['id_code'];
    /**
     * @var array
     */
    protected $fillable = [
        'currency',
        'currency_type',
        'http_response',
        'value',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
