<?php

declare(strict_types=1);

namespace Modules\Integrations\Transformers;

use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\ApiToken;
use Modules\Core\Entities\User;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ApiTokenResource
 * @property ApiToken $resource
 * @package Modules\Integrations\Transformers
 */
class ApiTokenSelectResource extends JsonResource
{
    /**
     * @var string
     */
    private $format = "d/m/Y";

    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     * @throws BindingResolutionException
     * @throws PresenterException
     */
    public function toArray($request)
    {
        $this->defineTimezone();
        $token = $this->resource->token;
        $revoked = $token->revoked ?? null;
        if ($this->resource->user) {
            $antifraudUrl = env("CHECKOUT_URL") . "/api/v1/antifraud/" . hashids()->encode($this->resource->user->id);
            $antifraudUrl = "https://" . str_replace(["http://", "https://"], "", $antifraudUrl);
        }

        return [
            "id_code" => Hashids::encode($this->resource->id),
            "access_token" => $this->resource->access_token,
            "antifraud_url" => $this->resource->user ? $antifraudUrl : "",
            "status" => $this->resource->present()->status(),
            "revoked" => $revoked,
            "register_date" => $this->getFormatDate($this->resource->created_at),
            "description" => $this->resource->description,
            "integration_type" => $this->resource->present()->getIntegrationType(),
            "scopes" => $this->resource->scopes,
        ];
    }

    /**
     * @return void
     */
    protected function defineTimezone()
    {
        $this->timezone = config("app.timezone");
        if (Auth::check() && auth()->user()->timezone) {
            /** @var User $user */
            $user = auth()->user();
            $this->timezone = $user->timezone;
        }
    }

    /**
     * @param Carbon $date
     * @return string
     */
    protected function getFormatDate($date)
    {
        if (empty($date)) {
            return null;
        }

        return Carbon::parse($date)->format($this->format);
    }
}
