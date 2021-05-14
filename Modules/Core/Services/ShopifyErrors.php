<?php

namespace Modules\Core\Services;

/**
 * Class ShopifyErrors
 * @package Modules\Core\Services
 */
class ShopifyErrors
{
    /**
     * @param $error
     * @return string
     */
    public static function FormatErrors($error)
    {
        if (strstr($error, "[API] Invalid API key or access token (unrecognized login or wrong password)", true)) {
            return 'Chave API inválida ou token de acesso (login não reconhecido ou senha incorreta)';
        } elseif (str_contains($error, 'This action requires merchant approval for read_themes scope')) {
            return 'Permissões no aplicativo para editar o template estão incorretas';
        }

        return null;
    }
}
