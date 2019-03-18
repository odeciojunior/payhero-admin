<?php

namespace Modules\Convites\Http\Controllers;

use App\Convite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Convites\Transformers\ConvitesResource;

class ConvitesApiController extends Controller {

    public function convites() {

        $convites = Convite::where('user_convite',\Auth::user()->id);

        return ConvitesResource::collection($convites->paginate());
    }

}
