<?php

namespace Modules\Shipping\Http\Controllers;

use App\Entities\Shipping;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;

class ShippingController extends Controller {

    public function store(Request $request) {

        $requestData = $request->all();

        $requestData['project'] = Hashids::decode($requestData['projeto'])[0];

        if($requestData['pre_selected']){
            $shippings = Shipping::where('project',$requestData['project'])->get()->toArray();
            foreach($shippings as $shipping){
                if($shipping['pre_selected']){
                    Shipping::find($shipping['id'])->update([
                        'pre_selected' => '0'
                    ]);
                }
            }
        }

        Shipping::create($requestData);

        return response()->json('success');
    }

    public function update(Request $request) {

        $requestData = $request->all();

        $shipping = Shipping::find($requestData['id']);

        if($shipping['pre_selected'] && (!$requestData['pre_selected'] || !$requestData['status'])){
            $s = Shipping::where([
                ['project', $shipping['project']],
                ['id', '!=', $shipping['id']],
            ])->first();
            if($s){
                $s->update([
                    'pre_selected' => '1'
                ]);
            }
        }
        if(!$shipping['pre_selected'] && $requestData['pre_selected']){
            $s = Shipping::where([
                ['project', $shipping['project']],
                ['pre_selected', '1'],
            ])->first();
            if($s){
                $s->update([
                    'pre_selected' => '0'
                ]);
            }
        }

        $shipping->update($requestData);

        return response()->json('success');
    }

    public function delete(Request $request) {

        $requestData = $request->all();

        $shipping = Shipping::find($requestData['id']);

        if($shipping['pre_selected']){
            $s = Shipping::where([
                ['project', $shipping['project']],
                ['id', '!=', $shipping['id']]
            ])->first();
            if($s){
                $s->update([
                    'pre_selected', '1'
                ]);
            }
        }

        $shipping->delete();

        return response()->json('success');
    }


}

