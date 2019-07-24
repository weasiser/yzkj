<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\OrderRequest;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\VendingMachine;
use App\Models\VendingMachineAisle;
use App\Transformers\OrderTransformer;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function store(OrderRequest $request, OrderTransformer $orderTransformer)
    {
        $user = $this->user();
        // 开启一个数据库事务
        $order = \DB::transaction(function () use ($user, $request) {
            $amount = $request->input('amount');
            $aisle = VendingMachineAisle::find($request->input('vending_machine_aisle_id'));
            $vendingMachine = VendingMachine::find($aisle->vending_machine_id);
            $product = Product::find($aisle->product_id);

            $sold_price = big_number($product->selling_price)->subtract($aisle->preferential_price)->getValue();
            $totalAmount = big_number($sold_price)->multiply($amount)->getValue();

            // 创建一个订单
            $order = new Order([
                'amount'       => $amount,
                'sold_price'   => $sold_price,
                'total_amount' => $totalAmount,
            ]);

            $order->vendingMachineAisle()->associate($aisle);
            $order->vendingMachine()->associate($vendingMachine);
            $order->product()->associate($product);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();

            if ($aisle->decreaseStock($amount) <= 0) {
                throw new \Exception('该货道的商品库存不足');
            }
            $this->dispatch(new CloseOrder($order, config('app.order_ttl')));

            return $order;
        });

        return $this->response->item($order, $orderTransformer)->setStatusCode(201);
    }

    public function show(Order $order, OrderTransformer $orderTransformer)
    {
        return $this->response->item($order, $orderTransformer);
    }

    public function destroy(Order $order)
    {
        $this->authorize('own', $order);
        $order->vendingMachineAisle->increaseStock($order->amount);
        $order->delete();
        return $this->response->noContent();
    }

    public function delivering(Order $order)
    {
        $this->authorize('own', $order);

        // 判断订单的发货状态是否为已发货
        if ($order->deliver_status !== Order::DELIVER_STATUS_PENDING) {
            throw new \Exception('出货状态不正确');
        }

        // 更新发货状态为已收到
        $order->update(['deliver_status' => Order::DELIVER_STATUS_DELIVERING]);

    }

    public function delivered(Order $order)
    {
        $this->authorize('own', $order);

        // 判断订单的发货状态是否为已发货
        if ($order->deliver_status !== Order::DELIVER_STATUS_DELIVERING) {
            throw new \Exception('出货状态不正确');
        }

        // 更新发货状态为已收到
        $order->update(['deliver_status' => Order::DELIVER_STATUS_DELIVERED]);
    }

    public function userOrders(Order $order, OrderTransformer $orderTransformer)
    {
        $user = $this->user();

        $orders = $user->orders()->recent()->paginate(10);

        return $this->response->paginator($orders, $orderTransformer);
    }
}
