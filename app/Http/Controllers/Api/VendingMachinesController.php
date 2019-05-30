<?php

namespace App\Http\Controllers\Api;

use App\Models\VendingMachine;
use App\Transformers\VendingMachineTransformer;
use Illuminate\Http\Request;

class VendingMachinesController extends Controller
{
    public function show(VendingMachine $vending_machine, Request $request, VendingMachineTransformer $vendingMachineTransformer)
    {
        app(\Dingo\Api\Transformer\Factory::class)->disableEagerLoading();

//        $vending_machine = $vending_machine->aisles;

        if ($request->include) {
            $vending_machine->load($request->include);
        }

        return $this->response->item($vending_machine, $vendingMachineTransformer);
    }
}
