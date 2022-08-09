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
            return "Chave API inválida ou token de acesso (login não reconhecido ou senha incorreta)";
        } elseif (str_contains($error, "This action requires merchant approval for read_themes scope")) {
            return "Permissões no aplicativo para editar o template estão incorretas";
        }

        return null;
    }

    public function FormatDataInvalidShopifyIntegration($e): string
    {
        $message = "Dados do shopify inválidos, revise os dados informados";
        if (method_exists($e, "getCode")) {
            switch ($e->getCode()) {
                case 401:
                    $message = "Dados do shopify inválidos, revise os dados informados";
                    break;
                case 402:
                    $message = "Pagamento pendente na sua loja do Shopify";
                    break;
                case 403:
                    $message = "Verifique as permissões de seu aplicativo no Shopify";
                    break;
                case 404:
                    $message = "Url da loja não encontrada, revise os dados informados";
                    break;
                case 423:
                    $message = "Loja bloqueada, entre em contato com o suporte do Shopify";
                    break;
                case 429:
                    $message = "Limite de requisiçoes atingido, tente novamente";
                    break;
                default:
                    $message = "Dados do shopify inválidos, revise os dados informados";
            }
        }

        if (method_exists($e, "getMessage") && strpos($e->getMessage(), "Shop name should be") !== false) {
            $message = "Url inválida, revise os dados informados";
        }

        return $message;
    }
}
