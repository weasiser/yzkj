<?php

namespace App\Http\Controllers\Api;

use App\Models\VendingMachineAisle;
use App\Transformers\VendingMachineAisleTransformer;
use Illuminate\Http\Request;

class VendingMachineAislesController extends Controller
{
    public function update(VendingMachineAisle $vendingMachineAisle, Request $request, VendingMachineAisleTransformer $vendingMachineAisleTransformer)
    {
        if ($request->input('is_opened') === 'change') {
            $vendingMachineAisle->is_opened = !$vendingMachineAisle->is_opened;
            $vendingMachineAisle->update();
        } elseif ($request->input('stock') === 'plus') {
            $vendingMachineAisle->increaseStock();
        } elseif ($request->input('stock') === 'minus') {
            $vendingMachineAisle->decreaseStock();
        }
//        return $this->response->item($vendingMachineAisle, $vendingMachineAisleTransformer);
//        return $this->response->array([
//            'stock' => $vendingMachineAisle->stock
//        ]);
        return $this->response->array([
            'updateResult' => 'success'
        ]);
    }
}
