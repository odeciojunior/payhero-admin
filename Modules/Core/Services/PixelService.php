<?php


namespace Modules\Core\Services;


use Exception;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Pixel;

class PixelService
{

    public function getPixelFacebookByProject(int $projectId)
    {
        $pixelModel = new Pixel();
        $affiliate = Affiliate::where('project_id', $projectId)
            ->where('user_id', auth()->user()->account_owner_id)
            ->first();

        $affiliateId = $affiliate->id ?? null;

        return $pixelModel->where('project_id', $projectId)
            ->where('platform', 'facebook')
            ->where('affiliate_id', $affiliateId)
            ->get();
    }

    public function updateCodeMetaTagFacebook(int $projectId, string $metaTag): bool
    {
        try {
            $pixels = $this->getPixelFacebookByProject($projectId);

            foreach ($pixels as $pixel) {
                $pixel->update(
                    [
                        'code_meta_tag_facebook' => $metaTag
                    ]
                );
            }

            return true;
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }
}