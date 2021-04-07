<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MoreActionForOrderRefund implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (isset($this->order->extra['return_to_stock'])) {
            $this->returnToStock($this->order);
        }
        if (isset($this->order->extra['disable_aisle'])) {
            $this->disableAisle($this->order);
        }
    }

    protected function returnToStock($order)
    {
        $order->vendingMachineAisle->increaseStock($order->refund_number);
//        $warehouse_id = $order->vendingMachine->warehouse->id;
//        $productPes = $order->product->productPesWithoutSoldOutChecked->where('stock', '<', 0)->where('warehouse_id', '=', $warehouse_id)->first();
//        if (!$productPes) {
//            $productPes = $order->product->productPesWithoutSoldOutChecked->where('warehouse_id', $warehouse_id)->first();
//        }
//        $productPes->update(['stock' => $productPes->stock + $order->refund_number]);
    }

    protected function disableAisle($order)
    {
        $vendingMachineAisle = $order->vendingMachineAisle;
        $vendingMachineAisle->is_opened = false;
        $vendingMachineAisle->update();
    }
}
