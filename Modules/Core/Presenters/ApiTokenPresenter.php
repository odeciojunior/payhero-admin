<?php

namespace Modules\Core\Presenters;

use App\Traits\FoxPresenterTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\Exceptions\PresenterException;
use Laracasts\Presenter\Presenter;
use Modules\Core\Entities\ApiToken;

/**
 * Class ApiTokenPresenter
 * @property ApiToken $entity
 * @package Modules\Core\Presenters
 */
class ApiTokenPresenter extends Presenter
{
    use FoxPresenterTrait;
    /**
     * @var array
     */
    public $enum = [
        "integration_type_enum" => [
            1 => 'admin',       //'Admin',
            2 => 'personal',    //'Acesso Pessoal',
            3 => 'external',    //'Integração Externa',
        ],
    ];
    /**
     * @var array
     */
    private $integrationTypeEnumScope = [
        1 => [    //'admin',
                  ApiToken::TOKEN_SCOPE_ADMIN,
        ],
        2 => [    //'personal',
                  ApiToken::TOKEN_SCOPE_USER,
                  ApiToken::TOKEN_SCOPE_SALE,
                  ApiToken::TOKEN_SCOPE_PRODUCT,
                  ApiToken::TOKEN_SCOPE_CLIENT,
        ],
        3 => [  //'external',
                ApiToken::TOKEN_SCOPE_SALE,
                ApiToken::TOKEN_SCOPE_PRODUCT,
        ],
    ];

    /**
     * @return int
     */
    public function getPersonalIntegrationTypeEnum()
    {
        return $this->getIntegrationType('personal');
    }

    /**
     * @param int|string $scope
     * @return int|string
     */
    public function getIntegrationType($scope = null)
    {
        $enum = $scope ?? $this->entity->integration_type_enum ?? null;
        if (empty($enum)) {
            return '';
        }

        return $this->getEnumGeneric('integration_type_enum', $enum);
    }

    /**
     * @return int
     */
    public function getExternalIntegrationTypeEnum()
    {
        return $this->getIntegrationType('external');
    }

    /**
     * @param int $integrationTypeEnum
     * @return array
     */
    public function getTokenScope($integrationTypeEnum = null)
    {
        $integrationType = $integrationTypeEnum ?? $this->entity->integration_type_enum;
        if (empty($integrationType) || !isset($this->integrationTypeEnumScope[$integrationType])) {
            return null;
        }

        return $this->integrationTypeEnumScope[$integrationType];
    }

    /**
     * @return string
     * @throws PresenterException
     * @throws BindingResolutionException
     */
    public function status()
    {
        $token = $this->entity->getValidToken();
        if (empty($token)) {
            return 'inactive';
        }
        /** @var bool $revoked */
        $revoked = $token->revoked ?? false;
        if ($revoked) {
            return 'inactive';
        }
        /** @var Carbon $now */
        $now = now();
        /** @var Carbon $expiresAt */
        $expiresAt = $token->expires_at ?? $now;
        if ($now->isAfter($expiresAt)) {
            return 'inactive';
        }

        return 'active';
    }
}
