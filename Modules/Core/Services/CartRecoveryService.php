<?php
/**
 * Created by PhpStorm.
 * User: gustavo
 * Date: 08/07/19
 * Time: 15:03
 */

namespace Modules\Core\Services;

use App\Entities\Checkout;
use App\Entities\Domain;
use App\Entities\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CartRecoveryService
{
    public function verifyAbandonedCarts()
    {
        $dateStart = new \DateTime();
        $dateEnd   = new \DateTime();

        $dateEnd->modify('-1 hour');
        $dateStart->modify('-2 hours');
        $formatted_dateStart = $dateStart->format('y-m-d H:i:s');
        $formatted_dateEnd   = $dateEnd->format('y-m-d H:i:s');

        $abandonedCarts = Checkout::where([['status', '=', 'abandoned cart'], ['created_at', '>', $formatted_dateStart], ['created_at', '<', $formatted_dateEnd]])
                                  ->with('projectModel')
                                  ->get();

        foreach ($abandonedCarts as $abandonedCart) {
            $log       = Log::where('id_log_session', $abandonedCart->id_log_session)->orderBy('created_at', 'desc')
                            ->first();
            $sendEmail = new SendgridService();
            $domain    = Domain::where('project_id', $abandonedCart->projectModel->id)->first();
            $link      = "https://checkout." . $domain['name'] . "/recovery/" . $log->id_log_session;
            $view      = view('core::emails.abandonedcart', compact('link'));
            $sendEmail->sendEmail($view, $link, 'noreply@cloudfox.app', 'cloudfox', $log['email'], $log['name']);
        }
    }
}
