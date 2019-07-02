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

        if ($request->include) {
            $vendingMachine->load($request->include);
        }

        return $this->response->collection($vendingMachine, $vendingMachineTransformer);
    }
}
