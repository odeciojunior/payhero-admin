<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\PostbackLogPresenter;

/**
 * @property integer $id
 * @property int $origin
 * @property string $description
 * @property mixed $data
 * @property string $created_at
 * @property string $updated_at
 */
class PostbackLog extends Model
{
    use PresentableTrait, FoxModelTrait;

    protected $presenter = PostbackLogPresenter::class;
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
        'origin', 
        'description', 
        'data', 
        'created_at', 
        'updated_at'
    ];

}
