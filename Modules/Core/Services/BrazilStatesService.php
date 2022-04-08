<?php

namespace Modules\Core\Services;

class BrazilStatesService
{
    public static function getStatePopulation($state)
    {
        if($state == "RR"){
            return 0.652713;
        }
        elseif($state == "AP"){
            return 0.877613;
        }
        elseif($state == "AM"){
            return 4.269995;
        }
        elseif($state == "PA"){
            return 8.777124;
        }
        elseif($state == "AC"){
            return 0.906876;
        }
        elseif($state == "RO"){
            return 1.815278;
        }
        elseif($state == "TO"){
            return 1.607363;
        }
        elseif($state == "MA"){
            return 7.153262;
        }
        elseif($state == "PI"){
            return 3.289290;
        }
        elseif($state == "CE"){
            return 9.240580;
        }
        elseif($state == "RN"){
            return 3.560903;
        }
        elseif($state == "PB"){
            return 4.059905;
        }
        elseif($state == "PE"){
            return 9.674793;
        }
        elseif($state == "AL"){
            return 3.365351;
        }
        elseif($state == "SE"){
            return 2.338474;
        }
        elseif($state == "BA"){
            return 14.985284;
        }
        elseif($state == "MT"){
            return 3.567234;
        }
        elseif($state == "DF"){
            return 3.094325;
        }
        elseif($state == "GO"){
            return 7.206589;
        }
        elseif($state == "MS"){
            return 2.839188;
        }
        elseif($state == "MG"){
            return 21.411923;
        }
        elseif($state == "ES"){
            return 4.108508;
        }
        elseif($state == "RJ"){
            return 17.463349;
        }
        elseif($state == "SP"){
            return 46.649132;
        }
        elseif($state == "PR"){
            return 11.597484;
        }
        elseif($state == "SC"){
            return 7.338473;
        }
        elseif($state == "RS"){
            return 11.466630;
        }
    }
}

