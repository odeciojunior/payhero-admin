<?php

declare(strict_types=1);

namespace Modules\Api\Validators\V1;

class TrackingsRule
{
    protected static $rules = [
        'sale_id' => 'required',
        'product_id' => 'required',
        'tracking_code' => 'required|min:9|max:18'
    ];

    public static function storeTrackings()
    {
        return [
            'sale_id' => self::$rules['sale_id'],
            'product_id' => self::$rules['product_id'],
            'tracking_code' => self::$rules['tracking_code']
        ];
    }

    public static function updateTrackings()
    {
        return [
            'tracking_code' => self::$rules['tracking_code']
        ];
    }

    public static function messages()
    {
        return [
            'sale_id.required' => 'O id da venda é obrigatório.',
            'product_id.required' => 'O id do produto é obrigatório.',
            'tracking_code.required' => 'O código de rastreio é obrigatório.',
            'tracking_code.min' => 'O código de rastreio deve ter pelo menos 9 caracteres.',
            'tracking_code.max' => 'O código de rastreio não pode ter mais de 18 caracteres.',
            'tracking_code.regex' => 'O código de rastreio é inválido.'
        ];
    }
}
