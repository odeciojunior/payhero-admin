<?php

namespace Modules\Core\Services;

/**
 * Class LogisticRouterService
 * @package Modules\Core\Services
 */
class LogisticRouterService
{
    /**
     * @param $trackingCode
     * @return string
     * @see https://docs.aftership.com/api/4/couriers/get-couriers
     */
    public function getLogistic($trackingCode)
    {
        switch (true) {
            case $this->isChinaPost($trackingCode);
                return 'china-post';
            case $this->isChinaEMS($trackingCode);
                return 'china-ems';
            case $this->isSwissPost($trackingCode);
                return 'swiss-post';
            case $this->isYanwen($trackingCode);
                return 'yanwen';
            case $this->isPostNL($trackingCode);
                return 'postnl-international';
            case $this->isSFExpress($trackingCode);
                return 'sf-express';
            case $this->is4PX($trackingCode);
                return '4px';
            case $this->isLaoPost($trackingCode);
                return 'lao-post';
            case $this->isSingaporePost($trackingCode);
                return 'singapore-post';
            case $this->isJerseyPost($trackingCode);
                return 'jersey-post';
            case $this->isCorreios($trackingCode);
                return 'brazil-correios';
            default:
                return '';
        }
    }

    /**
     * @param $trackingCode
     * @return bool
     */
    private function isChinaPost($trackingCode)
    {
        return preg_match('/LO[0-9]{9}CN/', $trackingCode) != false;
    }

    /**
     * @param $trackingCode
     * @return bool
     */
    private function isChinaEMS($trackingCode)
    {
        return preg_match('/LL[0-9]{9}CN/', $trackingCode) != false;
    }

    /**
     * @param $trackingCode
     * @return bool
     */
    private function isYanwen($trackingCode)
    {
        return preg_match('/[A-Z]{2}[0-9]{9}YP/', $trackingCode) != false;
    }

    /**
     * @param $trackingCode
     * @return bool
     */
    private function isPostNL($trackingCode)
    {
        return preg_match('/[A-Z]{2}[0-9]{9}NL/', $trackingCode) != false;
    }

    /**
     * @param $trackingCode
     * @return bool
     */
    private function isSFExpress($trackingCode)
    {
        return preg_match('/SF[0-9]{13}/', $trackingCode) != false;
    }

    /**
     * @param $trackingCode
     * @return bool
     */
    private function is4PX($trackingCode)
    {
        return preg_match('/LP00[0-9]{12}/', $trackingCode) != false;
    }

    /**
     * @param $trackingCode
     * @return bool
     */
    private function isLaoPost($trackingCode)
    {
        return preg_match('/UA[0-9]{9}LA/', $trackingCode) != false;
    }

    /**
     * @param $trackingCode
     * @return bool
     */
    private function isSwissPost($trackingCode)
    {
        return preg_match('/[A-Z]{2}[0-9]{9}CH/', $trackingCode) != false;
    }

    /**
     * @param $trackingCode
     * @return bool
     */
    private function isSingaporePost($trackingCode)
    {
        return preg_match('/SY[0-9]{11}/', $trackingCode) != false;
    }

    /**
     * @param $trackingCode
     * @return bool
     */
    private function isJerseyPost($trackingCode)
    {
        return preg_match('/[A-Z]{2}[0-9]{9}CN/', $trackingCode) != false;
    }

    /**
     * @param $trackingCode
     * @return bool
     */
    private function isCorreios($trackingCode)
    {
        if (preg_match('/[A-Z]{2}[0-9]{9}[A-Z]{2}/', $trackingCode)) {

            /**
             * //verifica se as letras iniciais sao usadas nos codigos de rastreio dos correios
             * @see https://www.correios.com.br/precisa-de-ajuda/como-rastrear-um-objeto/siglas-utilizadas-no-rastreamento-de-objeto
             */
            /*$siglas = [
                "AL", "AR", "AS",
                "BE", "BF", "BG", "BH", "BI",
                "CA", "CB", "CC", "CD", "CE", "CF", "CG", "CH", "CI", "CJ", "CK", "CL", "CM", "CN", "CO", "CP", "CQ", "CR", "CS", "CT", "CU", "CV", "CW", "CX", "CY", "CZ",
                "DA", "DB", "DC", "DD", "DE", "DF", "DG", "DI", "DJ", "DK", "DL", "DM", "DN", "DO", "DP", "DQ", "DR", "DS", "DT", "DU", "DV", "DW", "DX", "DY", "DZ",
                "EA", "EB", "EC", "ED", "EE", "EF", "EG", "EH", "EI", "EJ", "EK", "EL", "EM", "EN", "EO", "EP", "EQ", "ER", "ES", "ET", "EU", "EV", "EW", "EX", "EY", "EZ",
                "FA", "FB", "FC", "FD", "FE", "FF", "FH", "FJ", "FM", "FR",
                "IA", "IC", "ID", "IE", "IF", "II", "IK", "IM", "IN", "IP", "IR", "IS", "IT", "IU", "IX",
                "JA", "JB", "JC", "JD", "JE", "JF", "JG", "JH", "JI", "JJ", "JK", "JL", "JM", "JN", "JO", "JP", "JQ", "JR", "JS", "JT", "JU", "JV", "JW", "JX", "JY", "JZ",
                "LA", "LB", "LC", "LD", "LE", "LF", "LG", "LH", "LI", "LJ", "LK", "LL", "LM", "LN", "LP", "LQ", "LS", "LV", "LW", "LX", "LY", "LZ",
                "MA", "MB", "MC", "MD", "ME", "MF", "MH", "MK", "MM", "MP", "MS", "MT", "MY", "MZ",
                "NE", "NX",
                "OA", "OB", "OC", "OD", "OG",
                "PA", "PB", "PC", "PD", "PE", "PF", "PG", "PH", "PI", "PJ", "PK", "PL", "PM", "PN", "PO", "PP", "PR", "PS",
                "RA", "RB", "RC", "RD", "RE", "RF", "RG", "RH", "RI", "RJ", "RK", "RL", "RM", "RN", "RO", "RP", "RQ", "RR", "RS", "RT", "RU", "RV", "RW", "RX", "RY", "RZ",
                "SA", "SB", "SC", "SD", "SE", "SF", "SG", "SH", "SI", "SJ", "SK", "SL", "SM", "SN", "SO", "SP", "SQ", "SR", "SS", "ST", "SU", "SV", "SW", "SX", "SY", "SZ",
                "TC", "TE", "TR", "TS",
                "VA", "VC", "VD", "VE", "VF", "VV",
                "XA", "XM", "XR", "XX",
            ];

            if (in_array(substr($trackingCode, 0, 2), $siglas)) {
                return true;
            }*/
            return true;
        }
        return false;
    }
}
