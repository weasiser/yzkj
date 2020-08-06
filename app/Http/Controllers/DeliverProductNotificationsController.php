<?php

namespace App\Http\Controllers;

use App\Handlers\VendingMachineDeliverAndQuery;
use App\Models\DeliverProductNotification;
use App\Models\Order;
use App\Services\RefundService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

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
                        $order->vendingMachineAisle->decreaseStock();
//                    $order->product->productPes->where('stock', '>=', 1)->first()->decrement('stock', 1);
                        $warehouse_id = $order->vendingMachine->warehouse->id;
                        $productPes = $order->product->productPesWithoutSoldOutChecked->where([['stock', '>=', 1], ['warehouse_id', $warehouse_id]])->first();
                        if (!$productPes) {
                            $productPes = $order->product->productPesWithoutSoldOutChecked->where('warehouse_id', $warehouse_id)->last();
                        }
                        $productPes->update(['stock' => $productPes->stock - 1]);
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
}
