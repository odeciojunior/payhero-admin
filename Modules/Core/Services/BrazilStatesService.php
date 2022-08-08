<?php

namespace Modules\Core\Services;

class BrazilStatesService
{
    public static function getStatePopulation($state)
    {
        if ($state == "RR") {
            return 652713;
        } elseif ($state == "AP") {
            return 877613;
        } elseif ($state == "AM") {
            return 4269995;
        } elseif ($state == "PA") {
            return 8777124;
        } elseif ($state == "AC") {
            return 906876;
        } elseif ($state == "RO") {
            return 1815278;
        } elseif ($state == "TO") {
            return 1607363;
        } elseif ($state == "MA") {
            return 7153262;
        } elseif ($state == "PI") {
            return 3289290;
        } elseif ($state == "CE") {
            return 9240580;
        } elseif ($state == "RN") {
            return 3560903;
        } elseif ($state == "PB") {
            return 4059905;
        } elseif ($state == "PE") {
            return 9674793;
        } elseif ($state == "AL") {
            return 3365351;
        } elseif ($state == "SE") {
            return 2338474;
        } elseif ($state == "BA") {
            return 14985284;
        } elseif ($state == "MT") {
            return 3567234;
        } elseif ($state == "DF") {
            return 3094325;
        } elseif ($state == "GO") {
            return 7206589;
        } elseif ($state == "MS") {
            return 2839188;
        } elseif ($state == "MG") {
            return 21411923;
        } elseif ($state == "ES") {
            return 4108508;
        } elseif ($state == "RJ") {
            return 17463349;
        } elseif ($state == "SP") {
            return 46649132;
        } elseif ($state == "PR") {
            return 11597484;
        } elseif ($state == "SC") {
            return 7338473;
        } elseif ($state == "RS") {
            return 11466630;
        }
    }
}
