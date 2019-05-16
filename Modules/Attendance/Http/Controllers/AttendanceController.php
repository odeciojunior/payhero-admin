<?php

namespace Modules\Attendance\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class AttendanceController extends Controller {


    public function index() {

        return view('attendance::index');
    }

}
