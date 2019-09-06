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
                'purchase_price' => $product->buying_price,
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

            $aisle->decreaseStock($amount);

//            if ($aisle->decreaseStock($amount) <= 0) {
//                throw new \Exception('该货道的商品库存不足');
//            }
            $this->dispatch(new CloseOrder($order, config('app.order_ttl')));

            return $order;
        });

        return $this->response->item($order, $orderTransformer)->setStatusCode(201);
    }

    public function index(Request $request, Order $order, OrderTransformer $orderTransformer)
    {
        if ($request->date) {
            $orders = $order->whereDate('paid_at', $request->date)->recent()->paginate(5);
            return $this->response->paginator($orders, $orderTransformer);
        } elseif ($request->orderNo) {
            $searchOrder = $order->where('no', $request->orderNo)->first();
            if ($searchOrder) {
                return $this->response->item($searchOrder, $orderTransformer);
            } else {
                return $this->response->noContent();
            }
        }
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

    public function getStatistics(Request $request, Order $order, Product $product, VendingMachine $vendingMachine)
    {
        if ($request->vendingMachineId) {
            $productSaleStatistics = $vendingMachine->find($request->vendingMachineId)->vendingMachineAisles->leftJoin('orders', 'vending_machine_aisles.id', '=', 'orders.vending_machine_aisle_id')->leftJoin('products', 'orders.product_id', '=', 'products.id')->whereYear('orders.paid_at', $request->year)->whereMonth('orders.paid_at', $request->month)->selectRaw('products.id, products.title, products.image, sum(orders.amount - orders.refund_number) as sold_count, sum(orders.total_amount - orders.refund_amount) as sold_value, sum((orders.sold_price - orders.purchase_price) * (orders.amount - orders.refund_number)) as sold_profit')->groupBy('products.id')->get();

            $dailySaleStatistics = $order->where('vending_machine_id', $request->vendingMachineId)->whereYear('paid_at', $request->year)->whereMonth('paid_at', $request->month)->selectRaw('date(paid_at) as date, sum(amount - refund_number) as sold_count, sum(total_amount - refund_amount) as sold_value, sum((sold_price - purchase_price) * (amount - refund_number)) as sold_profit')->groupBy('date')->get();

            $totalStatistics = $order->where('vending_machine_id', $request->vendingMachineId)->whereYear('paid_at', $request->year)->whereMonth('paid_at', $request->month)->selectRaw('sum(orders.amount - orders.refund_number) as sold_count, sum(orders.total_amount - orders.refund_amount) as sold_value, sum((orders.sold_price - orders.purchase_price) * (orders.amount - orders.refund_number)) as sold_profit')->get();
        } else {
            $productSaleStatistics = $product->leftJoin('orders', 'products.id', '=', 'orders.product_id')->where('products.on_sale', true)->whereYear('orders.paid_at', $request->year)->whereMonth('orders.paid_at', $request->month)->selectRaw('products.id, products.title, products.image, sum(orders.amount - orders.refund_number) as sold_count, sum(orders.total_amount - orders.refund_amount) as sold_value, sum((orders.sold_price - orders.purchase_price) * (orders.amount - orders.refund_number)) as sold_profit')->groupBy('products.id')->orderBy('sold_count', 'asc')->get();

            $dailySaleStatistics = $order->whereYear('paid_at', $request->year)->whereMonth('paid_at', $request->month)->selectRaw('date(paid_at) as date, sum(total_amount - refund_amount) as info, sum(amount - refund_number) as sold_count, sum(total_amount - refund_amount) as sold_value, sum((sold_price - purchase_price) * (amount - refund_number)) as sold_profit')->groupBy('date')->get();

            $totalStatistics = $order->whereYear('paid_at', $request->year)->whereMonth('paid_at', $request->month)->selectRaw('sum(orders.amount - orders.refund_number) as sold_count, sum(orders.total_amount - orders.refund_amount) as sold_value, sum((orders.sold_price - orders.purchase_price) * (orders.amount - orders.refund_number)) as sold_profit')->get();
        }

        foreach ($dailySaleStatistics as $key => $value) {
            $value['info'] = '￥' . $value['info'];
            $value['data'] = [
                'sold_count' => $value['sold_count'],
                'sold_value' => $value['sold_value'],
                'sold_profit' => $value['sold_profit'],
            ];
            unset($value['sold_count'], $value['sold_value'], $value['sold_profit']);
        }
//        return $order->whereYear('orders.paid_at', $request->year)->whereMonth('orders.paid_at', $request->month)->leftJoin('products', 'orders.product_id', '=', 'products.id')->selectRaw('products.id, products.title, products.image, sum(orders.amount - orders.refund_number) as sold_count, sum(orders.total_amount - orders.refund_amount) as sold_value, sum((orders.sold_price - orders.purchase_price) * (orders.amount - orders.refund_number)) as sold_profit')->groupBy('orders.product_id')->get();
//        return $order->whereYear('paid_at', $request->year)->whereMonth('paid_at', $request->month)->selectRaw('date(paid_at) as date, sum(amount - refund_number) as sold_count, sum(total_amount - refund_amount) as sold_value, sum((sold_price - purchase_price) * (amount - refund_number)) as sold_profit')->groupBy('date')->get();
        return $this->response->array([
            'productSaleStatistics' => $productSaleStatistics,
            'dailySaleStatistics' => $dailySaleStatistics,
            'totalStatistics' => $totalStatistics,
        ]);
    }

//    public function delivering(Order $order)
//    {
//        $this->authorize('own', $order);
//
//        // 判断订单的发货状态是否为已发货
//        if ($order->deliver_status !== Order::DELIVER_STATUS_PENDING) {
//            throw new \Exception('出货状态不正确');
//        }
//
//        // 更新发货状态为已收到
//        $order->update(['deliver_status' => Order::DELIVER_STATUS_DELIVERING]);
//
//    }
//
//    public function delivered(Order $order)
//    {
//        $this->authorize('own', $order);
//
//        // 判断订单的发货状态是否为已发货
//        if ($order->deliver_status !== Order::DELIVER_STATUS_DELIVERING) {
//            throw new \Exception('出货状态不正确');
//        }
//
//        // 更新发货状态为已收到
//        $order->update(['deliver_status' => Order::DELIVER_STATUS_DELIVERED]);
//    }

    public function userOrders(Order $order, OrderTransformer $orderTransformer)
    {
        $user = $this->user();

        $orders = $user->orders()->recent()->paginate(5);

        return $this->response->paginator($orders, $orderTransformer);
    }

    public function applyRefund(Order $order)
    {
        // 校验订单是否属于当前用户
        $this->authorize('own', $order);
        // 判断订单是否已付款
        if (!$order->paid_at) {
            throw new \Exception('该订单未支付，不可退款');
        }
        // 判断订单退款状态是否正确
        if ($order->refund_status !== Order::REFUND_STATUS_PENDING) {
            throw new \Exception('该订单已经申请过退款，请勿重复申请');
        }
        // 将用户输入的退款理由放到订单的 extra 字段中
//        $extra                  = $order->extra ?: [];
//        $extra['refund_reason'] = $request->input('reason');
        // 将订单退款状态改为已申请退款
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
        ]);

        return $this->response->array([
            'applyRefundResult' => 'success',
        ]);
    }
}
