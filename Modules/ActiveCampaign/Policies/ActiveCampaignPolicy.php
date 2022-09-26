<?php

namespace Modules\ActiveCampaign\Policies;

use Modules\Core\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Entities\ActivecampaignIntegration;

class ActiveCampaignPolicy
{
    use HandlesAuthorization;

    /**
     * @return bool
     */
    public function edit(User $user, ActivecampaignIntegration $activeCampaignIntegration)
    {
        return $user->account_owner_id == $activeCampaignIntegration->user_id;
    }

    /**
     * @return bool
     */
    public function update(User $user, ActivecampaignIntegration $activeCampaignIntegration)
    {
        return $user->account_owner_id == $activeCampaignIntegration->user_id;
    }

    /**
     * @return bool
     */
    public function destroy(User $user, ActivecampaignIntegration $activeCampaignIntegration)
    {
        return $user->account_owner_id == $activeCampaignIntegration->user_id;
    }

    /**
     * @return bool
     */
    public function show(User $user, ActivecampaignIntegration $activeCampaignIntegration)
    {
        return $user->account_owner_id == $activeCampaignIntegration->user_id;
    }
}
