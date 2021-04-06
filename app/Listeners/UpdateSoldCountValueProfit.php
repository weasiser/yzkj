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
        $warehouse = $vendingMachine->warehouse;
        if ($order->refund_status === Order::REFUND_STATUS_SUCCESS) {
            $sold_count = $order->refund_number;
            $sold_value = $order->refund_amount;
            $sold_profit = big_number($order->sold_price)->subtract($order->purchase_price)->multiply($order->refund_number)->getValue();
            $product->sold_count -= $sold_count;
            $product->sold_value -= $sold_value;
            $product->sold_profit -= $sold_profit;
            $vendingMachine->sold_count -= $sold_count;
            $vendingMachine->sold_value -= $sold_value;
            $vendingMachine->sold_profit -= $sold_profit;
            $product->update();
            $vendingMachine->update();
            if ($warehouse) {
                $warehouse->sold_count -= $sold_count;
                $warehouse->sold_value -= $sold_value;
                $warehouse->sold_profit -= $sold_profit;
                $warehouse->update();
            }
            if ($refundOrderFeedback = $order->refundOrderFeedback) {
                $refundOrderFeedback->is_handled = true;
                $refundOrderFeedback->save();
            }
        } else {
            $sold_count = $order->amount;
            $sold_value = $order->total_amount;
            $sold_profit = big_number($order->sold_price)->subtract($order->purchase_price)->multiply($order->amount)->getValue();
            $product->sold_count += $sold_count;
            $product->sold_value += $sold_value;
            $product->sold_profit += $sold_profit;
            $vendingMachine->sold_count += $sold_count;
            $vendingMachine->sold_value += $sold_value;
            $vendingMachine->sold_profit += $sold_profit;
            $product->update();
            $vendingMachine->update();
            if ($warehouse) {
                $warehouse->sold_count += $sold_count;
                $warehouse->sold_value += $sold_value;
                $warehouse->sold_profit += $sold_profit;
                $warehouse->update();
            }
        }
    }
}
