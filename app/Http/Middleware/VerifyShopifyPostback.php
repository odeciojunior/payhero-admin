<?php

namespace App\Http\Middleware;

use App\Entities\Project;
use Closure;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class VerifyShopifyPostback
{
    /**
     * @param $data
     * @param $hmac_header
     * @param $myShopifyToken
     * @return bool
     */
    function verify_webhook($data, $hmac_header, $myShopifyToken)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $myShopifyToken, true));

        return hash_equals($hmac_header, $calculated_hmac);
    }

    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $projectModel = new Project();

        $projectId = current(Hashids::decode($request->project_id));

        if ($projectId) {
            //hash ok
            $project = $projectModel->with(['shopifyIntegrations'])
                                    ->whereNotNull('shopify_id')
                                    ->find($projectId);

            $shopifySharedSecret = $project->shopifyIntegrations->first()->shared_secret;

            if (empty($shopifySharedSecret)) {
                //shared nao preenchido
//                return response()->json([
//                                            'message' => 'unauthorized',
//                                        ], 400);

            } else {
                $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
                $data        = file_get_contents('php://input');
                $verified    = $this->verify_webhook($data, $hmac_header, $shopifySharedSecret);

                if ($verified != true) {
                    //webhook nao validado
                    return response()->json([
                                                'message' => 'unauthorized',
                                            ], 400);
                }
            }
        } else {
            //problema com hash
            return response()->json([
                                        'message' => 'unauthorized',
                                    ], 400);
        }

        return $next($request);
    }
}
