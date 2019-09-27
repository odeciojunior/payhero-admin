<?php

namespace Modules\Trackings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\ProductPlanSale;
use Vinkla\Hashids\Facades\Hashids;

class TrackingsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('trackings::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('trackings::create');
    }

    /**
     * @param Request $request
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function store(Request $request)
    {
        $data                 = $request->all();
        $productPlanSaleModel = new ProductPlanSale();
        if (!empty($data['tracking_code']) && !empty($data['sale_id']) && !empty($data['product_id'])) {
            $saleId    = current(Hashids::decode($data['sale_id']));
            $productId = current(Hashids::decode($data['product_id']));
            if ($saleId && $productId) {
                $productPlanSale = $productPlanSaleModel->where([['sale_id', $saleId], ['product_id', $productId]])
                                                        ->first();
                if ($productPlanSale) {
                    $productPlanSale->update([
                                                 'tracking_code'        => $data['tracking_code'],
                                                 'tracking_status_enum' => $productPlanSaleModel->present()
                                                                                                ->getStatusEnum('posted'),
                                             ]);
                }
            }
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('trackings::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('trackings::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
