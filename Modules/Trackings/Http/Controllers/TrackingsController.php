<?php

namespace Modules\Trackings\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TrackingsController extends Controller
{

    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('trackings::index');
    }

    /**
     * @param $filename
     * @return BinaryFileResponse
     */
    public function download($filename)
    {
        $file_path = storage_path('app/' . $filename);
        if (file_exists($file_path)) {
            return response()->download($file_path, $filename, [
                'Content-Length: ' . filesize($file_path)
            ])->deleteFileAfterSend(true);
        } else {
            abort(404);
        }
    }
}
