<?php

namespace App\Http\Controllers\Api;

use App\Handlers\VendingMachineDeliverAndQuery;
use App\Models\DeliverProductNotification;
use App\Models\Product;
use App\Models\UniDeliverProductNotification;
use App\Models\VendingMachine;
use App\Models\VendingMachineAisle;
use App\Models\YiputengDeliverProductNotification;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class VMDeliverAndQueryController extends Controller
{
    public function deliverProduct(Request $request)
    {
        $vendingMachineId = $request->input('vendingMachineId');
        $ordinal = $request->input('ordinal');
        $orderNo = $request->input('orderNo') ?: date('YmdHis') . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $vendingMachine = VendingMachine::find($vendingMachineId);

//        $http = new Client();

//        $params = [
//            'goodslist' => [
//                [
//                    'cabid' => '1',
//                    'cabtype' => '1',
//                    'latticeId' => (string)$ordinal,
//                    'resultid' => '1'
//                ]
//            ],
//            'machineId' => (string)$vendingMachine->code,
//            'orderid' => (string)$orderNo
//        ];
//
//        dispatch(function () use ($http, $params) {
//            $http->post('yzkj.test/deliverProductNotifications/notify', [
//                'json' => $params
//            ]);
//        })->delay(now()->addSeconds(5));

//        $http->post('yzkj.test/deliverProductNotifications/notify', [
//            'json' => $params
//        ]);

        return app(VendingMachineDeliverAndQuery::class)->deliverProduct($vendingMachine->code, $orderNo, $ordinal, $vendingMachine->cabinet_id, $vendingMachine->cabinet_type);

//        dispatch(new DeliverProduct($vendingMachine, $ordinal, $orderNo))->onQueue($vendingMachine->code);

//        return $this->response->array([
//            'result' => '200'
//        ]);
    }

    public function queryMachineInfo(Request $request)
    {
        return app(VendingMachineDeliverAndQuery::class)->queryMachineInfo($request->input('code'));
    }

    public function queryCommodityInfo(Request $request)
    {
        return app(VendingMachineDeliverAndQuery::class)->queryCommodityInfo($request->input('code'));
    }

    public function queryDeliverStatus(Request $request, VendingMachineAisle $vendingMachineAisle, Product $product)
    {
        $orderNo = $request->input('orderNo');
        $deliverProductNotification = DeliverProductNotification::where('no', $orderNo)->first();
        if ($deliverProductNotification) {
//            if ($request->input('realDeal') === 'yes' && $deliverProductNotification->result === '1') {
//                $vendingMachineAisle->find($request->input('vendingMachineAisleId'))->decreaseStock();
//                $product->find($request->input('productId'))->productPes->where('stock', '>=', 1)->first()->decrement('stock', 1);
//            }
            return $this->response->array([
                'result' => $deliverProductNotification->result
            ]);
        } else {
            return $this->response->array([
                'result' => '0'
            ]);
        }
    }

    public function queryYiputengDeliverStatus(Request $request)
    {
        $trade_no = $request->input('trade_no');
        $deliverProductNotification = YiputengDeliverProductNotification::where('trade_no', $trade_no)->first();
        if ($deliverProductNotification) {
            return $this->response->array([
                'result' => $deliverProductNotification->result
            ]);
        }
    }

    public function queryVendingMachineApiStatus(Request $request)
    {
        $machine_api_type = (int)$request->input('machine_api_type');
        $code = $request->input('code');
        $ordinal = (int)$request->input('ordinal');
        $status = false;
        $message = '';
        if ($machine_api_type === 0) {
            $result = app(VendingMachineDeliverAndQuery::class)->queryMachineInfo($code);
            if ($result['result'] === '200') {
                if ($result['data'][0]['netStat'] === 1) {
                    $status = true;
                    $message = '正常';
                } else {
                    $status = false;
                    $message = '机器不在线';
                }
            } else {
                $status = false;
                $message = '出货接口异常';
            }
        } elseif ($machine_api_type === 1) {
            $result = app(VendingMachineDeliverAndQuery::class)->queryMachineList();
            if ($result['code'] === 0) {
                foreach ($result['content'] as $key => $value) {
                    if ($value['machine_id'] === $code) {
                        if ($value['is_online'] === '在线') {
                            $result = app(VendingMachineDeliverAndQuery::class)->queryShelfList($code);
                            if ($result['code'] === 0) {
                                if ($result['content'][$ordinal - 1]['is_broken'] === 0) {
                                    $status = true;
                                    $message = '正常';
                                } else {
                                    $status = false;
                                    $message = '该货道已损坏';
                                }
                            } else {
                                $status = false;
                                $message = '出货接口异常';
                            }
                        } else {
                            $status = false;
                            $message = '机器不在线';
                        }
                        break;
                    }
                }
            } else {
                $status = false;
                $message = '出货接口异常';
            }
        }
        return $this->response->array([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function getApiToken()
    {
        return app(VendingMachineDeliverAndQuery::class)->getApiToken();
    }

    public function queryMachineList()
    {
        return app(VendingMachineDeliverAndQuery::class)->queryMachineList();
    }

    public function queryShelfList(Request $request)
    {
        return app(VendingMachineDeliverAndQuery::class)->queryShelfList($request->input('machine_id'));
    }

    public function payDelivery(Request $request)
    {
        $params = $request->input();
        $result = app(VendingMachineDeliverAndQuery::class)->payDelivery($params);
        if ($result['code'] === 0) {
            $notification = new YiputengDeliverProductNotification([
                'trade_no' => $params['trade_no'],
                'machine_id' => $params['machine_id'],
                'shelf_id' => $params['shelf_id'],
                'amount' => 1,
                'result' => 'DELIVERING'
            ]);

            $notification->save();
        }
        return $result;
    }

    public function payMultiDelivery(Request $request)
    {
        $params = $request->input();
        $result = app(VendingMachineDeliverAndQuery::class)->payMultiDelivery($params);
        if ($result['code'] === 0) {
            $multi_pay = json_decode(str_replace(':','":',str_replace('{', '{"', $params['multi_pay'])), true);
            foreach ($multi_pay[0] as $key => $value) {
                $shelf_id = $key;
                $amount = $value;
            }
            $notification = new YiputengDeliverProductNotification([
                'trade_no' => $params['trade_no'],
                'machine_id' => $params['machine_id'],
                'shelf_id' => $shelf_id,
                'amount' => $amount,
                'result' => 'DELIVERING'
            ]);

            $notification->save();
        }
        return $result;
    }

    public function deliverProductTest(VendingMachine $vendingMachine, Request $request)
    {
        $aisle_number = $request->input('aisle_number');
        $number = (int)$request->input('number');
        $machine_api_type = $vendingMachine->machine_api_type;
        $machine_id = $vendingMachine->code;
        $order_no = date('YmdHis') . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $params = [
            'order_no' => $order_no,
            'machine_id' => $machine_id,
            'aisle_number' => $aisle_number,
            'number' => $number,
            'result' => 'queueing'
        ];
        if ($machine_api_type === 0) {
            $params['number'] = 1;
            for ($i = 1; $i <= $number; $i++) {
                $params['order_no'] = $order_no . 'a' . $i;
                if ($i === $number) {
                    $params['extra']['last'] = true;
                }
                $uniDeliverProductNotification = new UniDeliverProductNotification($params);
                $uniDeliverProductNotification->save();
            }
        } elseif ($machine_api_type === 1) {
            $uniDeliverProductNotification = new UniDeliverProductNotification($params);
            $uniDeliverProductNotification->save();
        }
        return $this->response->accepted();
    }
}
