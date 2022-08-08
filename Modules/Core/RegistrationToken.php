<?php

namespace Modules\Register\Entities;

use Illuminate\Database\Eloquent\Model;

class RegistrationToken extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = ["type", "type_data", "token", "expiration", "number_wrong_attempts", "ip", "validated"];

    protected $table = "registration_token";
}
