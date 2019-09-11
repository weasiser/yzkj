<?php

namespace App\Observers;

use App\Models\ProductPes;

class ProductPesObserver
{
    public function saved(ProductPes $productPes)
    {
        $this->updateTotalStockAndMinExpirationDate($productPes);
    }

//    public function updated(ProductPes $productPes)
//    {
//        $this->updateTotalStockAndMinExpirationDate($productPes);
//    }

    public function deleted(ProductPes $productPes)
    {
        $this->updateTotalStockAndMinExpirationDate($productPes);
    }

    protected function updateTotalStockAndMinExpirationDate($productPes)
    {
        $product = $productPes->product;
        $productPes = $product->productPes;
        $product->min_expiration_date = $product->productPesWithoutSoldOutChecked->min('expiration_date');
        $total_stock = $productPes->sum('stock');
        $total_registered_stock = $productPes->sum('registered_stock');
        $product->total_stock = $total_stock;
        $product->total_registered_stock = $total_registered_stock;
        $product->warehouse_stock = $total_stock - $product->vending_machine_stock;
        $product->save();
    }
}
