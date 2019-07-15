<?php

namespace App\Http\Controllers\Api;

use App\Handlers\VendingMachineDeliverAndQuery;
use App\Jobs\DeliverProduct;
use App\Models\VendingMachine;
use Illuminate\Http\Request;

class VMDeliverAndQueryController extends Controller
{
    public function deliverProduct(Request $request)
    {
        $vendingMachineId = $request->input('vendingMachineId');
        $ordinal = $request->input('ordinal');
        $orderId = date('YmdHis') . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $vendingMachine = VendingMachine::find($vendingMachineId);

//        return app(VendingMachineDeliverAndQuery::class)->deliverProduct($vendingMachine->code, $orderId, $ordinal, $vendingMachine->cabinet_id, $vendingMachine->cabinet_type);
        dispatch(new DeliverProduct($vendingMachine, $ordinal, $orderId))->onQueue($vendingMachine->code);

        return $this->response->array([
            'deliverProductOnQueue' => 'success'
        ]);
    }

    public function queryMachineInfo(Request $request)
    {
        return app(VendingMachineDeliverAndQuery::class)->queryMachineInfo($request->input('machineUuid'));
    }
}
