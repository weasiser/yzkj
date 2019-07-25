<?php

namespace App\Http\Controllers\Api;

use App\Models\VendingMachineAisle;
use App\Transformers\ProductTransformer;
use App\Transformers\VendingMachineAisleTransformer;
use Illuminate\Http\Request;

class VendingMachineAislesController extends Controller
{
    public function update(VendingMachineAisle $vendingMachineAisle, Request $request, VendingMachineAisleTransformer $vendingMachineAisleTransformer, ProductTransformer $productTransformer)
    {
        if ($request->input('is_opened') === 'change') {
            $vendingMachineAisle->is_opened = !$vendingMachineAisle->is_opened;
            $vendingMachineAisle->update();
        } elseif ($request->input('stock') === 'plus') {
            $vendingMachineAisle->increaseStock();
        } elseif ($request->input('stock') === 'minus') {
            $vendingMachineAisle->decreaseStock();
        } elseif ($request->input('max_stock') === 'plus') {
            $vendingMachineAisle->increment('max_stock');
        } elseif ($request->input('max_stock') === 'minus') {
            $vendingMachineAisle->decrement('max_stock');
        } elseif ($request->input('stock') === 'full') {
            $vendingMachineAisle->stock = $vendingMachineAisle->max_stock;
            $vendingMachineAisle->update();
        } elseif ($request->input('is_lead_rail') === 'change') {
            $vendingMachineAisle->is_lead_rail = !$vendingMachineAisle->is_lead_rail;
            $vendingMachineAisle->update();
        } elseif ($product_id = $request->input('product_id')) {
            $vendingMachineAisle->product_id = $product_id;
            $vendingMachineAisle->update();
            $product = $vendingMachineAisle->product;
            return $this->response->item($product, $productTransformer);
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
