<?php


namespace Modules\Core\Services;

/**
 * Class ErrorsService
 * @package Modules\Core\Services
 */
class CloudflareErrorsService
{
    /**
     * @param $e
     * @return string
     */
    public static function formatErrorException($e)
    {
        $message = '';
        $response = json_decode($e->getResponse()->getBody()->getContents(), true);

        if (isset($response['success']) && $response['success'] == false) {
            foreach ($response['errors'] as $error) {
                $message .= $error['message'] . '! ';
                if (isset($error['error_chain'])) {
                    foreach ($error['error_chain'] as $errorChain) {
                        $message .= $errorChain['message'];
                    }
                }
            }
        } elseif (strstr(
            $e->getMessage(),
            "You cannot use this API for domains with a .cf, .ga, .gq, .ml, or .tk TLD (top-level domain)"
        )) {
            $message = 'Dominios (.cf, .ga, .gq, .ml, ou .tk) não podem ser cadastrados ou atualizados';
        } else {
            $message = 'Ocorreu um erro, tente novamente mais tarde!';
            report($e);
        }

        return $message;
    }
}