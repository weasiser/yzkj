<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\OrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\VendingMachine;
use App\Models\VendingMachineAisle;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function store(OrderRequest $request)
    {
        $user = $this->user();
        // 开启一个数据库事务
        $order = \DB::transaction(function () use ($user, $request) {
            $aisle = VendingMachineAisle::find($request->input('vending_machine_aisle_id'));
            $vendingMachine = VendingMachine::find($aisle->vending_machine_id);
            $product = Product::find($aisle->product_id);

            $sold_price = big_number($product->selling_price)->subtract($aisle->preferential_price)->getValue();
            $totalAmount = big_number($sold_price)->multiply($request->input('amount'))->getValue();

            // 创建一个订单
            $order   = new Order([
                'ordinal'       => $aisle->ordinal,
                'amount'        => $request->input('amount'),
                'sold_price'  => $sold_price,
                'total_amount' => $totalAmount,
            ]);

            $order->vendingMachine()->associate($vendingMachine);
            $order->product()->associate($product);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();

            return $order;
        });

        return $order;
    }
}
