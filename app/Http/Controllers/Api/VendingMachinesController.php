<?php

namespace App\Http\Controllers\Api;

use App\Models\VendingMachine;
use App\Transformers\VendingMachineTransformer;
use Illuminate\Http\Request;

class VendingMachinesController extends Controller
{
    public function show(VendingMachine $vendingMachine, Request $request, VendingMachineTransformer $vendingMachineTransformer)
    {
        app(\Dingo\Api\Transformer\Factory::class)->disableEagerLoading();

        if ($request->include) {
            $vendingMachine->load($request->include);
        }

        return $this->response->item($vendingMachine, $vendingMachineTransformer);
    }

    public function index(VendingMachine $vendingMachine, Request $request, VendingMachineTransformer $vendingMachineTransformer)
    {
        app(\Dingo\Api\Transformer\Factory::class)->disableEagerLoading();

        $vendingMachines = $vendingMachine->all();
        if ($request->include) {
            $vendingMachines->load($request->include);
        }

        return $this->response->collection($vendingMachines, $vendingMachineTransformer);
    }

    public function update(VendingMachine $vendingMachine, Request $request)
    {
        if ($request->input('is_opened') === 'change') {
            $vendingMachine->is_opened = !$vendingMachine->is_opened;
            $vendingMachine->update();
        }
        return $this->response->array([
            'updateResult' => 'success'
        ]);
    }

    public function isDelivering(VendingMachine $vendingMachine)
    {
        return $this->response->array([
            'isDelivering' => $vendingMachine->is_delivering
        ]);
    }

//    public function isDeliveringChange(VendingMachine $vendingMachine)
//    {
//        $vendingMachine->is_delivering = true;
//        $vendingMachine->update();
//        dispatch(function () use ($vendingMachine) {
//            if ($vendingMachine->is_delivering) {
//                $vendingMachine->is_delivering = false;
//                $vendingMachine->update();
//            }
//        })->delay(now()->addSeconds(60));
//        return $this->response->array([
//            'isDelivering' => $vendingMachine->is_delivering
//        ]);
//    }
}
