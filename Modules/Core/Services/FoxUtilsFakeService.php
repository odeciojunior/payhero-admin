<?php

namespace Modules\Core\Services;

use Illuminate\Support\Arr;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Sale;

class FoxUtilsFakeService
{
    public static function getRandomOperationalSystem(){
        
        $operationalSystem = [
            [ 'so'=>"iOS",'version'=>rand(8,15).'_'.rand(0,9).'_'.rand(0,9),'is_mobile'=>true,'enum'=>Checkout::OPERATIONAL_SYSTEM_IOS],
            [ 'so'=>"ChromeOS", 'version'=>rand(8172,14469).'.'.rand(45,99).'.'.rand(0,5), 'is_mobile'=>false,'enum'=>Checkout::OPERATIONAL_SYSTEM_CHROME],
            [ 'so'=>"OpenBSD",'version'=>'','is_mobile'=>false,'enum'=>Checkout::OPERATIONAL_SYSTEM_LINUX],
            [ 'so'=>"BlackBerryOS", 'version'=> '10.0.9.2372', 'is_mobile'=>false,'enum'=>Checkout::OPERATIONAL_SYSTEM_BLACK_BERRY],
            [ 'so'=>"AndroidOS", 'version'=> rand(4,12).'.'.rand(0,9).'.'.rand(0,9),'is_mobile'=>true,'enum'=>Checkout::OPERATIONAL_SYSTEM_ANDROID],
            [ 'so'=>'Windows','version'=>rand(5,11).'.'.rand(0,9),'is_mobile'=>false,'enum'=>Checkout::OPERATIONAL_SYSTEM_WINDOWS],
            [ 'so'=>'OS X','version'=>rand(10,12).'_'.rand(0,15).'_'.rand(0,9), 'is_mobile'=>false,'enum'=>Checkout::OPERATIONAL_SYSTEM_UNKNOWN]
        ];

        return Arr::random($operationalSystem);
    }

    public static function getRandomBrowser(){
        $browsers = [
                "Chrome - ".rand(99,102).'.'.rand(0,1).'.'.rand(1150,9999).'.'.rand(0,99),
                "Opera - ".rand(10,90).'.'.rand(0,3).'.'.rand(1754,3606).'.'.rand(61072,65175),
                "Edge - 101.0.4951.64".rand(97,102).'.'.rand(0,1).'.'.rand(1210,4951).'.'.rand(10,90),
                "Opera Mini - 4.4.33576".rand(2,4).'.'.rand(0,4).'.'.rand(17540,33576),
                "Firefox - ".rand(98,101).'.'.rand(0,3),
                "Safari - 15.4.1".rand(13,15).'.'.rand(0,4).'.'.rand(0,9),
                "UCBrowser - 11.3.5.908".rand(8,11).'.'.rand(0,3).'.'.rand(0,9).'.'.rand(100,908),               
        ]; 
        
        return Arr::random($browsers);
    }

    public static function getRandomUf(){
        $ufs = [
            'AC','AL','AM','AP','BA','CE','DF','ES','GO','MA','MG','MT','PA','PB','PE','PI','PR','RJ','RN','RO','RR','RS','SC','SP','TO'
        ];

        return Arr::random($ufs);
    }

    public static function getRandoFlagCC(){
        $flags = ['visa','mastercard','aura','discover','hipercard','amex','elo','diners','jcb'];
        return Arr::random($flags);
    }
    
    public static function getRandomStatus($paymentMethod){
        
        $status = [Sale::STATUS_PENDING, Sale::STATUS_APPROVED];//Sale::STATUS_CANCELED
        if($paymentMethod == Sale::CREDIT_CARD_PAYMENT){
            $status = [Sale::STATUS_APPROVED];//,Sale::STATUS_CANCELED_ANTIFRAUD, Sale::STATUS_IN_REVIEW,sale::STATUS_REFUSED
        }
        return Arr::random($status);
    }

}