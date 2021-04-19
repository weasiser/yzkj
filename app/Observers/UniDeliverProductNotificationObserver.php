<?php

namespace App\Observers;

use App\Jobs\DeliverProduct;
use App\Models\Order;
use App\Models\UniDeliverProductNotification;
use App\Models\VendingMachine;

class UniDeliverProductNotificationObserver
{
    public function created(UniDeliverProductNotification $uniDeliverProductNotification)
    {
        $vendingMachine = VendingMachine::where('code', $uniDeliverProductNotification->machine_id)->first();
        DeliverProduct::dispatch($uniDeliverProductNotification, $vendingMachine->machine_api_type)->onQueue($vendingMachine->code);
    }

    public function updated(UniDeliverProductNotification $uniDeliverProductNotification)
    {
        if (isset($uniDeliverProductNotification->extra['last'])) {
            $no = explode('a', $uniDeliverProductNotification->order_no)[0];
            $order = Order::where('no', $no)->first();
            if ($order) {
                if ($uniDeliverProductNotification->result === '1') {
                    $order->update(['deliver_status' => Order::DELIVER_STATUS_DELIVERED]);
                } elseif ($uniDeliverProductNotification->result === '2') {
                    $order->update(['deliver_status' => Order::DELIVER_STATUS_TIMEOUT]);
                } elseif ($uniDeliverProductNotification->result === '3') {
                    $order->update(['deliver_status' => Order::DELIVER_STATUS_FAILED]);
                }
            }
        } elseif (!stripos($uniDeliverProductNotification->order_no, 'a')) {
            $order = Order::where('no', $uniDeliverProductNotification->order_no)->first();
            if ($order) {
                if ($uniDeliverProductNotification->result === 'SUCCESS') {
                    $order->update(['deliver_status' => Order::DELIVER_STATUS_DELIVERED]);
                } elseif ($uniDeliverProductNotification->result === 'FAIL') {
                    $order->update(['deliver_status' => Order::DELIVER_STATUS_FAILED]);
                }
            }
        }
    }
}
