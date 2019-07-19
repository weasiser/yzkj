<?php

namespace App\Http\Controllers\Api;

use App\Handlers\VendingMachineDeliverAndQuery;
use App\Jobs\DeliverProduct;
use App\Models\DeliverProductNotification;
use App\Models\VendingMachine;
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

        $http = new Client();

        $params = [
            'goodslist' => [
                [
                    'cabid' => '1',
                    'cabtype' => '1',
                    'latticeId' => (string)$ordinal,
                    'resultid' => '1'
                ]
            ],
            'machineId' => (string)$vendingMachine->code,
            'orderid' => (string)$orderNo
        ];

        $http->post('yzkj.test/deliverProductNotifications/notify', [
            'json' => $params
        ]);

//        return app(VendingMachineDeliverAndQuery::class)->deliverProduct($vendingMachine->code, $orderNo, $ordinal, $vendingMachine->cabinet_id, $vendingMachine->cabinet_type);

//        dispatch(new DeliverProduct($vendingMachine, $ordinal, $orderNo))->onQueue($vendingMachine->code);

        return $this->response->array([
            'result' => '200'
        ]);
    }

    public function queryMachineInfo(Request $request)
    {
        return app(VendingMachineDeliverAndQuery::class)->queryMachineInfo($request->input('machineUuid'));
    }

    public function queryDeliverStatus(Request $request)
    {
        $orderNo = $request->input('orderNo');
        $deliverProductNotification = DeliverProductNotification::where('no', $orderNo)->first();
        if ($deliverProductNotification) {
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
