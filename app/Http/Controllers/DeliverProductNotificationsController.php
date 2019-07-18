<?php

namespace App\Http\Controllers;

use App\Models\DeliverProductNotification;
use Illuminate\Http\Request;

class DeliverProductNotificationsController extends Controller
{
    public function deliverProductNotify(Request $request)
    {
        $result = $request->input();

        if ($result) {
            $notification = new DeliverProductNotification([
                'no' => $result['orderid'],
                'code' => $result['machineId'],
                'ordinal' => $result['goodslist'][0]['latticeId'],
                'cabid' => $result['goodslist'][0]['cabid'],
                'cabtype' => $result['goodslist'][0]['cabtype'],
                'result' => $result['goodslist'][0]['resultid']
            ]);

            $notification->save();

            return json_encode(array('result'=>'200', 'resultDesc'=>'Success'));
        } else {
            return json_encode(array('result'=>'404', 'resultDesc'=>'Not Found'));
        }
    }
}
