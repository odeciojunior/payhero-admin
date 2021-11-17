<?php

namespace Modules\Finances\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

/**
 * Class FinancesController
 * @package Modules\Finances\Http\Controllers
 */
class FinancesController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
         return view('finances::multi');
    }

    public function show()
    {
        return view('finances::index');
    }

    /**
     * @return Factory|View
     */
    public function oldIndex()
    {
        return view('finances::old-index');
    }

    public function download($filename)
    {
     
        $file_path = storage_path('app/' . $filename);
        if (file_exists($file_path)) {
            return response()->download($file_path, $filename, [
                'Content-Length: ' . filesize($file_path)
            ]);
            //->deleteFileAfterSend(true);
        } else {
            abort(404);
        }
    }


}


