<?php

namespace App\Http\Controllers;

use App\Handlers\VendingMachineDeliverAndQuery;
use App\Models\DeliverProductNotification;
use App\Models\Order;
use App\Services\RefundService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class DeliverProductNotificationsController extends Controller
{
    public function deliverProductNotify(Request $request, RefundService $refundService)
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

//            $http = new Client();
//
//            $http->post('https://yzkj01.com/notice/deliverResult', [
//                'json' => $result
//            ]);

            if (strlen($result['orderid']) === 22) {
                $orderNo = substr($result['orderid'], 0, 20);
                $num = (int)ltrim(substr($result['orderid'], -2, 2), '0');
                $order = Order::where('no', $orderNo)->first();
                if ($order) {
                    if ($result['goodslist'][0]['resultid'] === '1') {
                        $this->decreaseStock($order);
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

//                            return app(VendingMachineDeliverAndQuery::class)->deliverProduct($result['machineId'], $orderNo, $result['goodslist'][0]['latticeId'], $result['goodslist'][0]['cabid'], $result['goodslist'][0]['cabtype']);
                            $result = app(VendingMachineDeliverAndQuery::class)->deliverProduct($result['machineId'], $orderNo, $result['goodslist'][0]['latticeId'], $result['goodslist'][0]['cabid'], $result['goodslist'][0]['cabtype']);
                            if ($result['result'] === '200') {
                                return json_encode(array('result'=>'200', 'resultDesc'=>'Success'));
                            } else {
                                $extra = $order->extra;
                                $extra['deliver_failed_code'] = $result;
                                $order->update([
                                    'deliver_status' => Order::DELIVER_STATUS_FAILED,
                                    'extra' => $extra
                                ]);
                                $this->refund($num, $order);
                            }
                        } else {
                            $order->update(['deliver_status' => Order::DELIVER_STATUS_DELIVERED]);
                        }
                    } elseif ($result['goodslist'][0]['resultid'] === '2') {
                        $order->update(['deliver_status' => Order::DELIVER_STATUS_TIMEOUT]);
                        $this->refund($num, $order);
                    } elseif ($result['goodslist'][0]['resultid'] === '3') {
                        $order->update(['deliver_status' => Order::DELIVER_STATUS_FAILED]);
                        $this->refund($num, $order);
                    }
                    $vendingMachine = $order->vendingMachine;
                    if ($vendingMachine->is_delivering) {
                        $vendingMachine->is_delivering = false;
                        $vendingMachine->update();
                    }
                    return json_encode(array('result'=>'200', 'resultDesc'=>'Success'));
                } else {
                    return json_encode(array('result'=>'404', 'resultDesc'=>'Not Found'));
                }
            }

            return json_encode(array('result'=>'200', 'resultDesc'=>'Success'));
        } else {
            return json_encode(array('result'=>'404', 'resultDesc'=>'Not Found'));
        }
    }

    protected function refund($num, Order $order)
    {
        $refundService = app(RefundService::class);
        $refundAmount = $order->amount - $num + 1;
        $refundService->miniappRefund($order, $refundAmount);
    }

    protected function decreaseStock($order)
    {
        $order->vendingMachineAisle->decreaseStock();
        $warehouse_id = $order->vendingMachine->warehouse->id;
        $productPes = $order->product->productPesWithoutSoldOutChecked->where('stock', '>=', 1)->where('warehouse_id', '=', $warehouse_id)->first();
        if (!$productPes) {
            $productPes = $order->product->productPesWithoutSoldOutChecked->where('warehouse_id', $warehouse_id)->last();
        }
        $productPes->update(['stock' => $productPes->stock - 1]);
    }

    public function yiputengDeliverProductNotify(Request $request)
    {
        $params = $request->input();
        $verifyResult = $this->verifySign($params);
        if ($verifyResult) {
            $http = new Client();
            $http->post('https://www.yzkj01.com/notice/yiputengDeliverResult', [
                'json' => [
                    'out_trade_no' => $params['out_trade_no'],
                    'trade_status' => $params['trade_status']
                ]
            ]);
            echo 'success';
        } else {
            echo 'fail';
        }
    }

    protected function verifySign($params)
    {
        $params['delivery_shelf'] = urlencode($params['delivery_shelf']);
        $sign = Arr::pull($params, 'sign');
        $str = '';
        ksort($params);
        foreach ($params as $k => $v) {
            //为key/value对生成一个key=value格式的字符串，并拼接到待签名字符串后面
            $str .= "$k=$v&";
        }

        $appSecret = config('services.yiputeng_vending_machine.app_secret');

        $str .= 'key=' . $appSecret;
        if ($sign === md5($str)) {
            return true;
        } else {
            return false;
        }
    }
}
