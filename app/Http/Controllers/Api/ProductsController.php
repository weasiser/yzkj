<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Transformers\ProductTransformer;
use GuzzleHttp\Client;
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
        $availableProductStock = $product->where('products.on_sale', true)->selectRaw('products.title, products.image, products.vending_machine_stock, sum(vending_machine_aisles.max_stock - vending_machine_aisles.stock) as available_product_stock, sum(vending_machine_aisles.stock) as available_machine_product_stock')->leftjoin('vending_machine_aisles', 'products.id', '=', 'vending_machine_aisles.product_id')->leftjoin('vending_machines', 'vending_machine_aisles.vending_machine_id', '=', 'vending_machines.id')->where('vending_machines.is_opened', true)->groupBy('products.id')->orderBy('products.id', 'desc')->get();
        foreach ($availableProductStock as $value) {
            $value['image'] = config('filesystems.disks.oss.cdnDomain') ? config('filesystems.disks.oss.cdnDomain') . '/' . $value['image'] . '-product' : Storage::disk(config('admin.upload.disk'))->url($value['image']) . '-product';
        }
        return $this->response->array([
            'availableProductStock' => $availableProductStock,
        ]);
    }

    public function updateGoodsDateInfoMonthly(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
        $product = Product::join('orders', function ($leftjoin) use ($year, $month) {
            if ($year) {
                $leftjoin = $leftjoin->whereYear('orders.paid_at', $year);
            }
            if ($month) {
                $leftjoin = $leftjoin->whereMonth('orders.paid_at', $month);
            }
            $leftjoin->on('products.id', '=', 'orders.product_id');
        });
        $productSaleStatistics = $product->where('products.on_sale', true)->selectRaw('products.title, sum(orders.amount - orders.refund_number) as sold_count')->groupBy('products.id')->orderBy('sold_count', 'desc')->get();
        $http = new Client();
        $response = $http->post('https://www.yzkj01.com/notice/update_goods_date_info', [
            'json' => $productSaleStatistics
        ]);
        return $response;
    }
}
