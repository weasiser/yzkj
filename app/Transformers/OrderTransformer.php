<?php

namespace App\Transformers;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user', 'product', 'vendingMachine', 'vendingMachineAisle', 'refundOrderFeedback'];

    public function transform(Order $order)
    {
        return [
            'id'                       => $order->id,
            'no'                       => $order->no,
            'user_id'                  => $order->user_id,
            'product_id'               => $order->product_id,
            'vending_machine_id'       => $order->vending_machine_id,
            'vending_machine_aisle_id' => $order->vending_machine_aisle_id,
            'amount'                   => $order->amount,
            'purchase_price'           => (float) $order->purchase_price,
            'sold_price'               => (float) $order->sold_price,
            'total_amount'             => (float) $order->total_amount,
            'paid_at'                  => $order->paid_at ? $order->paid_at->toDateTimeString() : null,
            'payment_method'           => $order->payment_method,
            'payment_no'               => $order->payment_no,
            'refund_status'            => $order->refund_status,
            'refund_no'                => $order->refund_no,
            'refund_amount'            => $order->refund_amount ? (float) $order->refund_amount : null,
            'refund_number'            => $order->refund_number ? $order->refund_amount : null,
            'deliver_status'           => $order->deliver_status,
//            'extra'                    => $order->extra,
            'created_at'               => (string) $order->created_at,
            'updated_at'               => (string) $order->updated_at,
        ];
    }

    public function includeUser(Order $order)
    {
        return $this->item($order->user, new UserTransformer());
    }

    public function includeProduct(Order $order)
    {
        return $this->item($order->product, new ProductTransformer());
    }

    public function includeVendingMachine(Order $order)
    {
        return $this->item($order->vendingMachine, new VendingMachineTransformer());
    }

    public function includeVendingMachineAisle(Order $order)
    {
        return $this->item($order->vendingMachineAisle, new VendingMachineAisleTransformer());
    }

    public function includeRefundOrderFeedback(Order $order)
    {
        if ($order->refundOrderFeedback) {
            return $this->item($order->refundOrderFeedback, new RefundOrderFeedbackTransformer());
        }
    }
}
