<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GetnetPostBack
 * @package Modules\Core\Entities
 */
class GetnetPostBack extends Model
{
    use FoxModelTrait;

    /**
     * @var string
     */
    protected $table = "getnet_postbacks";
    /**
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var string[]
     */
    protected $fillable = ["data", "created_at", "updated_at"];
}
