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

    public function updateCodeMetaTagFacebook(int $projectId, $metaTag): bool
    {
        try {
            $pixels = $this->getPixelFacebookByProject($projectId);

            if (empty($metaTag)) {
                $pixel = Pixel::where('project_id', $projectId)
                    ->where('platform', 'facebook')
                    ->whereNotNull('code_meta_tag_facebook')->first();

                $metaTag = $pixel->code_meta_tag_facebook;
            }

            if (!empty($metaTag)) {
                foreach ($pixels as $pixel) {
                    $pixel->update(
                        [
                            'code_meta_tag_facebook' => $metaTag
                        ]
                    );
                }
            }
            return true;
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }
}