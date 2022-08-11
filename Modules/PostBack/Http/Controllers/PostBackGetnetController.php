<?php

namespace Modules\PostBack\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Core\Entities\GetnetPostBack;

/**
 * Class PostBackGetnetController
 * @package Modules\PostBack\Http\Controllers
 */
class PostBackGetnetController
{
    /**
     * @var GetnetPostBack
     */
    private $getnetPostBackLogModel;

    /**
     * PostBackGetnetController constructor.
     * @param GetnetPostBack $getnetPostBack
     */
    public function __construct(GetnetPostBack $getnetPostBack)
    {
        $this->getnetPostBackLogModel = $getnetPostBack;
    }

    /**
     * @param Request $request
     */
    public function postBackGetnet(Request $request)
    {
        try {
            $requestData = $request->all();

            $this->getnetPostBackLogModel->create([
                "data" => json_encode($requestData),
            ]);
        } catch (\Exception $e) {
            report($e);
        }
    }
}
