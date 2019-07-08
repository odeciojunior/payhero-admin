<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $origin
 * @property mixed $data
 * @property string $created_at
 * @property string $updated_at
 */
class PostbackLog extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['origin', 'data', 'description', 'created_at', 'updated_at'];

}
