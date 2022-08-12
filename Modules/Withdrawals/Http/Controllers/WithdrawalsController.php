<?php

namespace Modules\Withdrawals\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

/**
 * Class WithdrawalsController
 * @package Modules\Withdrawals\Http\Controllers
 */
class WithdrawalsController extends Controller
{
    public function download($filename)
    {
        $file_path = storage_path("app/" . $filename);
        if (file_exists($file_path)) {
            return response()->download($file_path, $filename, ["Content-Length: " . filesize($file_path)]);
            //->deleteFileAfterSend(true);
        } else {
            abort(404);
        }
    }
}
