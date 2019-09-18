<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Transformers\ProductTransformer;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Product $product, ProductTransformer $productTransformer)
    {
        return $this->response->collection($product::where('on_sale', '=', true)->get(), $productTransformer);
    }
}
