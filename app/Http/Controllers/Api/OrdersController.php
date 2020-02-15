<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\OrderRequest;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\VendingMachine;
use App\Models\VendingMachineAisle;
use App\Transformers\OrderTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

            $sold_price = big_number($product->selling_price)->subtract($aisle->preferential_price)->subtract($product->promotion_price)->getValue();
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

    public function getDailyStatistics(Request $request, Order $order)
    {
        $year = $request->year;
        $month = $request->month;
        if ($vendingMachineId = $request->vendingMachineId) {
            $order = $order->where('vending_machine_id', $vendingMachineId);
        }
        if ($warehouseId = $request->warehouseId) {
            $order = $order->leftJoin('vending_machines', 'orders.vending_machine_id', '=', 'vending_machines.id')->where('vending_machines.warehouse_id', $warehouseId);
        }
        $dailySaleStatistics = $order->selectRaw('date(orders.paid_at) as date, sum(orders.total_amount - orders.refund_amount) as info, sum(orders.amount - orders.refund_number) as sold_count, sum(orders.total_amount - orders.refund_amount) as sold_value, sum((orders.sold_price - orders.purchase_price) * (orders.amount - orders.refund_number)) as sold_profit')->whereYear('orders.paid_at', $year)->whereMonth('orders.paid_at', $month)->groupBy('date')->get();
        foreach ($dailySaleStatistics as $key => $value) {
            $value['info'] = '￥' . $value['info'];
            $value['data'] = [
                'sold_count' => $value['sold_count'],
                'sold_value' => $value['sold_value'],
                'sold_profit' => $value['sold_profit'],
            ];
            unset($value['sold_count'], $value['sold_value'], $value['sold_profit']);
        }
        return $dailySaleStatistics;
    }

    public function getProductSaleStatistics(Request $request, Product $product, VendingMachineAisle $vendingMachineAisle)
    {
        $dateRange = $request->dateRange;
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $vendingMachineId = $request->vendingMachineId;
//        if ($vendingMachineId = $request->vendingMachineId) {
//            $productSaleStatistics = $vendingMachineAisle->where('vending_machine_aisles.vending_machine_id', $vendingMachineId)->leftJoin('orders', function ($leftjoin) use ($dateRange, $year, $month, $day) {
//                if ($dateRange) {
//                    $rangeTime = Carbon::now()->subHours($dateRange * 24);
//                    $leftjoin = $leftjoin->where('orders.paid_at', '>=', $rangeTime);
//                } else {
//                    if ($year) {
//                        $leftjoin = $leftjoin->whereYear('orders.paid_at', $year);
//                    }
//                    if ($month) {
//                        $leftjoin = $leftjoin->whereMonth('orders.paid_at', $month);
//                    }
//                    if ($day) {
//                        $leftjoin = $leftjoin->whereDay('orders.paid_at', $day);
//                    }
//                }
//                $leftjoin->on('vending_machine_aisles.id', '=', 'orders.vending_machine_aisle_id')->on('vending_machine_aisles.product_id', '=', 'orders.product_id');
//            })->leftJoin('products', 'vending_machine_aisles.product_id', '=', 'products.id')->selectRaw('vending_machine_aisles.ordinal, any_value(products.title) as title, any_value(products.image) as image, sum(orders.amount - orders.refund_number) as sold_count, sum(orders.total_amount - orders.refund_amount) as sold_value, sum((orders.sold_price - orders.purchase_price) * (orders.amount - orders.refund_number)) as sold_profit')->groupBy('vending_machine_aisles.ordinal')->orderBy('vending_machine_aisles.ordinal')->get();
//            $product = $product->leftJoin('vending_machine_aisles', function ($leftjoin) use ($vendingMachineId) {
//                $leftjoin->on('product.id', '=', 'vending_machine_aisles.product_id')->where('vending_machine_aisles.id', $vendingMachineId);
//            });
//        } else {
//            $product = $product->leftJoin('orders', 'products.id', '=', 'orders.product_id');
            $product = $product->leftJoin('orders', function ($leftjoin) use ($dateRange, $year, $month, $day, $vendingMachineId) {
                if ($dateRange) {
                    $rangeTime = Carbon::now()->subHours($dateRange * 24);
                    $leftjoin = $leftjoin->where('orders.paid_at', '>=', $rangeTime);
                } else {
                    if ($year) {
                        $leftjoin = $leftjoin->whereYear('orders.paid_at', $year);
                    }
                    if ($month) {
                        $leftjoin = $leftjoin->whereMonth('orders.paid_at', $month);
                    }
                    if ($day) {
                        $leftjoin = $leftjoin->whereDay('orders.paid_at', $day);
                    }
                }
                if ($vendingMachineId) {
                    $leftjoin = $leftjoin->where('orders.vending_machine_id', $vendingMachineId);
                }
                $leftjoin->on('products.id', '=', 'orders.product_id');
            });
            if ($warehouseId = $request->warehouseId) {
                $product = $product->leftJoin('vending_machines', function ($leftjoin) use ($warehouseId) {
                    $leftjoin->on('orders.vending_machine_id', '=', 'vending_machines.id')->where('vending_machines.warehouse_id', $warehouseId);
                });
//                $vendingMachineAisle = $vendingMachineAisle->leftJoin('vending_machines', 'vending_machine_aisles.vending_machine_id', '=', 'vending_machines.id')->where('vending_machines.warehouse_id', $warehouseId);
            }
//            $productSaleStatistics = $vendingMachineAisle->leftJoin('orders', function ($leftjoin) {
//                $leftjoin->on('vending_machine_aisles.id', '=', 'orders.vending_machine_aisle_id')->on('vending_machine_aisles.product_id', '=', 'orders.product_id');
//            })->leftJoin('products', 'vending_machine_aisles.product_id', '=', 'products.id')->selectRaw('products.title, products.image, sum(orders.amount - orders.refund_number) as sold_count, sum(orders.total_amount - orders.refund_amount) as sold_value, sum((orders.sold_price - orders.purchase_price) * (orders.amount - orders.refund_number)) as sold_profit')->groupBy('products.id')->orderBy('sold_count', 'desc')->get();

            $productSaleStatistics = $product->where('products.on_sale', true)->selectRaw('products.title, products.image, sum(orders.amount - orders.refund_number) as sold_count, sum(orders.total_amount - orders.refund_amount) as sold_value, sum((orders.sold_price - orders.purchase_price) * (orders.amount - orders.refund_number)) as sold_profit')->groupBy('products.id')->orderBy('sold_count', 'desc')->get();
//        }

        foreach ($productSaleStatistics as $value) {
            $value['image'] = config('filesystems.disks.oss.cdnDomain') ? config('filesystems.disks.oss.cdnDomain') . '/' . $value['image'] . '-product' : Storage::disk(config('admin.upload.disk'))->url($value['image']) . '-product';
            if ($value['sold_count'] === null) {
                $value['sold_count'] = '0';
                $value['sold_value'] = '0.00';
                $value['sold_profit'] = '0.00';
            }
        }

        return $this->response->array([
            'productSaleStatistics' => $productSaleStatistics,
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
