<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Transformers\ProductTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    public function index(Product $product, ProductTransformer $productTransformer)
    {
        return $this->response->collection($product::where('on_sale', '=', true)->get(), $productTransformer);
    }

    public function getAvailableProductStock(Product $product)
    {
        $availableProductStock = $product->where('products.on_sale', true)->selectRaw('products.title, products.image, sum(vending_machine_aisles.max_stock - vending_machine_aisles.stock) as available_product_stock, sum(vending_machine_aisles.stock) as vending_machine_stock')->leftjoin('vending_machine_aisles', 'products.id', '=', 'vending_machine_aisles.product_id')->leftjoin('vending_machines', 'vending_machine_aisles.vending_machine_id', '=', 'vending_machines.id')->where('vending_machines.is_opened', true)->groupBy('products.id')->orderBy('products.id', 'desc')->get();
        foreach ($availableProductStock as $value) {
            $value['image'] = config('filesystems.disks.oss.cdnDomain') ? config('filesystems.disks.oss.cdnDomain') . '/' . $value['image'] . '-product' : Storage::disk(config('admin.upload.disk'))->url($value['image']) . '-product';
        }
        return $this->response->array([
            'availableProductStock' => $availableProductStock,
        ]);
    }
}
