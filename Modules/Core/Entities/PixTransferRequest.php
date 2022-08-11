<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;

class PixTransferRequest extends Model
{
    use FoxModelTrait;
    use PresentableTrait;

    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";

    protected $dates = ["created_at", "updated_at"];

    protected $fillable = ["withdrawal_id", "company_id", "pix_key", "value", "request", "response"];
}
