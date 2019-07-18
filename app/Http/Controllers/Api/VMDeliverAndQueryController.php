<?php

namespace App\Http\Controllers\Api;

use App\Handlers\VendingMachineDeliverAndQuery;
use App\Jobs\DeliverProduct;
use App\Models\DeliverProductNotification;
use App\Models\VendingMachine;
use Illuminate\Http\Request;

class VMDeliverAndQueryController extends Controller
{
    public function deliverProduct(Request $request)
    {
        $vendingMachineId = $request->input('vendingMachineId');
        $ordinal = $request->input('ordinal');
        $orderNo = $request->input('orderNo') ?: date('YmdHis') . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $vendingMachine = VendingMachine::find($vendingMachineId);

        return app(VendingMachineDeliverAndQuery::class)->deliverProduct($vendingMachine->code, $orderNo, $ordinal, $vendingMachine->cabinet_id, $vendingMachine->cabinet_type);
//        dispatch(new DeliverProduct($vendingMachine, $ordinal, $orderNo))->onQueue($vendingMachine->code);

//        return $this->response->array([
//            'deliverProductOnQueue' => 'success'
//        ]);
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
