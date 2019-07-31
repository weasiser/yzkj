<?php

namespace App\Http\Controllers;

use App\Handlers\VendingMachineDeliverAndQuery;
use App\Models\DeliverProductNotification;
use App\Models\Order;
use GuzzleHttp\Client;
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

            if (strlen($result['orderid']) === 22) {
                $orderNo = substr($result['orderid'], 0, 20);
                $order = Order::where('no', $orderNo)->first();
                $num = (int)ltrim(substr($result['orderid'], -2, 2), '0');
                if ($num < $order->amount) {
                    $num += 1;
                    if ($num < 10) {
                        $num = '0' . $num;
                    }
                    $orderNo .= $num;

//                    $http = new Client();
//
//                    $params = [
//                        'goodslist' => [
//                            [
//                                'cabid' => $result['goodslist'][0]['cabid'],
//                                'cabtype' => $result['goodslist'][0]['cabtype'],
//                                'latticeId' => $result['goodslist'][0]['latticeId'],
//                                'resultid' => '1'
//                            ]
//                        ],
//                        'machineId' => $result['machineId'],
//                        'orderid' => $orderNo
//                    ];

//                    $http->post('yzkj.test/deliverProductNotifications/notify', [
//                        'json' => $params
//                    ]);

//                    dispatch(function () use ($http, $params) {
//                        $http->post('yzkj.test/deliverProductNotifications/notify', [
//                            'json' => $params
//                        ]);
//                    })->delay(now()->addSeconds(5));

                    return app(VendingMachineDeliverAndQuery::class)->deliverProduct($result['machineId'], $orderNo, $result['goodslist'][0]['latticeId'], $result['goodslist'][0]['cabid'], $result['goodslist'][0]['cabtype']);
                } else {
                    return json_encode(array('result'=>'200', 'resultDesc'=>'Success'));
                }
            }

            return json_encode(array('result'=>'200', 'resultDesc'=>'Success'));
        } else {
            return json_encode(array('result'=>'404', 'resultDesc'=>'Not Found'));
        }
    }
}
