<?php

namespace Modules\Melhorenvio\Facades;

use Illuminate\Support\Facades\Facade;

class MelhorenvioFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "melhorenvio";
    }
}
