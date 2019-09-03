<?php

namespace App\Listeners;

use App\Events\OrderPaidOrRefunded;
use App\Models\Order;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateSoldCountValueProfit
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderPaidOrRefunded  $event
     * @return void
     */
    public function handle(OrderPaidOrRefunded $event)
    {
        // 从事件对象中取出对应的订单
        $order = $event->getOrder();
        $product = $order->product;
        $vendingMachine = $order->vendingMachine;
        if ($order->refund_status === Order::REFUND_STATUS_SUCCESS) {
            $product->sold_count -= $order->refund_number;
            $product->sold_value -= $order->refund_amount;
            $product->sold_profit -= big_number($order->sold_price)->subtract($order->purchase_price)->multiply($order->refund_number)->getValue();
            $vendingMachine->sold_count -= $order->refund_number;
            $vendingMachine->sold_value -= $order->refund_amount;
            $vendingMachine->sold_profit -= big_number($order->sold_price)->subtract($order->purchase_price)->multiply($order->refund_number)->getValue();
            $product->update();
            $vendingMachine->update();
        } else {
            $product->sold_count += $order->amount;
            $product->sold_value += $order->total_amount;
            $product->sold_profit += big_number($order->sold_price)->subtract($order->purchase_price)->multiply($order->amount)->getValue();
            $vendingMachine->sold_count += $order->amount;
            $vendingMachine->sold_value += $order->total_amount;
            $vendingMachine->sold_profit += big_number($order->sold_price)->subtract($order->purchase_price)->multiply($order->amount)->getValue();
            $product->update();
            $vendingMachine->update();
        }
    }
}
