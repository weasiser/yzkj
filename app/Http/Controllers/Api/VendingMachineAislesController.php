<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\VendingMachineAisle;
use App\Transformers\ProductTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendingMachineAislesController extends Controller
{
    public function update(VendingMachineAisle $vendingMachineAisle, Request $request, ProductTransformer $productTransformer)
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
            $productBefore = $vendingMachineAisle->product;
            $product = Product::find($product_id);
            $vendingMachineAisle->product()->associate($product);
//            $vendingMachineAisle->product_id = $product_id;
            $vendingMachineAisle->save();
            $this->updateTotalVendingMachineStock($productBefore);
            $this->updateTotalVendingMachineStock($product);
            return $this->response->item($product, $productTransformer);
        } elseif ($request->input('is_sold_out_checked') === 'change') {
            $vendingMachineAisle->is_sold_out_checked = true;
            $vendingMachineAisle->update();
            $product = $vendingMachineAisle->product;
            $vendingMachineAisles = $product->vendingMachineAisles;
            $vendingMachineAislesChecked = $vendingMachineAisles->where('is_sold_out_checked', true);
            if ($vendingMachineAisles->count() === $vendingMachineAislesChecked->count()) {
                $product->productPesWithoutSoldOutChecked->first()->update(['is_sold_out_checked' => true]);
//                $vendingMachineAislesChecked->each->update(['is_sold_out_checked' => false]);
                DB::table('vending_machine_aisles')->where('product_id', $product->id)->where('is_sold_out_checked', true)->update(['is_sold_out_checked' => false, 'updated_at' => Carbon::now()]);
                $product->min_expiration_date = $product->productPesWithoutSoldOutChecked->min('expiration_date');
                $product->update();
            }
        }
//        return $this->response->item($vendingMachineAisle, $vendingMachineAisleTransformer);
//        return $this->response->array([
//            'stock' => $vendingMachineAisle->stock
//        ]);
        return $this->response->array([
            'updateResult' => 'success'
        ]);
    }

    protected function updateTotalVendingMachineStock($product)
    {
        $vendingMachineAisles = $product->vendingMachineAisles;
        $vendingMachineStock = $vendingMachineAisles->sum('stock');
        $product->vending_machine_stock = $vendingMachineStock;
        $product->warehouse_stock = $product->total_stock - $vendingMachineStock;
        $product->save();
    }
}
