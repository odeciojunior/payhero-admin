<?php

namespace Modules\DemoAccount\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Invites\Http\Controllers\InvitesApiController;

class InvitesApiDemoController extends InvitesApiController
{    
    // public function getInvitationData(Request $request){

    //     $invitationSentCount = 6;
    //     return response()->json(
    //         [
    //             'message' => 'Dados dos convites retornados com sucesso',
    //             'data' => [
    //                 'invitation_accepted_count' => 1,
    //                 'invitation_sent_count' =>$invitationSentCount,
    //                 'commission_paid' => 'R$ ' . number_format(1258270 / 100, 2, ',', '.'),
    //                 'commission_pending' => 'R$ ' . number_format(52200 / 100, 2, ',', '.'),
    //                 'invitations_available' => auth()->user()->invites_amount - $invitationSentCount,
    //             ],
    //         ], 200
    //     );
    // }

}
