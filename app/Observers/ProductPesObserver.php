<?php

namespace App\Observers;

use App\Models\ProductPes;

class ProductPesObserver
{
    public function saved(ProductPes $productPes)
    {
        $this->updateTotalStockAndMinExpirationDate($productPes);
    }

    public function deleted(ProductPes $productPes)
    {
        $this->updateTotalStockAndMinExpirationDate($productPes);
    }

    protected function updateTotalStockAndMinExpirationDate($productPes)
    {
        $product = $productPes->product;
        $productPes = $product->productPes;
        $product->min_expiration_date = $productPes->min('expiration_date');
        $product->total_stock = $productPes->sum('stock');
        $product->save();
    }
}
