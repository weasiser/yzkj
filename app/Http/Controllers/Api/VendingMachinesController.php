<?php

namespace App\Http\Controllers\Api;

use App\Models\VendingMachine;
use App\Transformers\VendingMachineTransformer;
use Illuminate\Http\Request;

class VendingMachinesController extends Controller
{
    public function show(VendingMachine $vending_machine, VendingMachineTransformer $vendingMachineTransformer)
    {
        return $this->response->item($vending_machine, $vendingMachineTransformer);
    }
}
