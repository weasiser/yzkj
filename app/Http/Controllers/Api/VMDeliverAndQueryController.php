<?php

namespace App\Http\Controllers\Api;

use App\Handlers\VendingMachineDeliverAndQuery;
use App\Jobs\DeliverProduct;
use App\Models\DeliverProductNotification;
use App\Models\Product;
use App\Models\ProductPes;
use App\Models\VendingMachine;
use App\Models\VendingMachineAisle;
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
        return app(VendingMachineDeliverAndQuery::class)->queryMachineInfo($request->input('machineUuid'));
    }

    public function queryDeliverStatus(Request $request, VendingMachineAisle $vendingMachineAisle, Product $product)
    {
        $orderNo = $request->input('orderNo');
        $deliverProductNotification = DeliverProductNotification::where('no', $orderNo)->first();
        if ($deliverProductNotification) {
            if ($request->input('realDeal') === 'yes' && $deliverProductNotification->result === '1') {
                $vendingMachineAisle->find($request->input('vendingMachineAisleId'))->decreaseStock();
                $product->find($request->input('productId'))->productPes->where('stock', '>=', 1)->first()->decrement('stock', 1);
            }
            return $this->response->array([
                'result' => $deliverProductNotification->result
            ]);
        } else {
            return $this->response->array([
                'result' => '0'
            ]);
        }
    }
}
