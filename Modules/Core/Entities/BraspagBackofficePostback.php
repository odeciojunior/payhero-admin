<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

class BraspagBackofficePostback extends Model
{
    protected $keyType = 'integer';

    protected $fillable = ['data', 'created_at', 'updated_at'];
}
