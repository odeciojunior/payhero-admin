<?php 

namespace Modules\Core\Helpers;

class StringHelper {


    public static function randString($size){

        $basic = 'abcdefghijlmnopqrstuvwxyz0123456789';

        $parametro = "";

        for($count= 0; $size > $count; $count++){
            $parametro.= $basic[rand(0, strlen($basic) - 1)];
        }

        return $parametro;
    }

}
