<?php

namespace App\Jobs;

use App\Handlers\VendingMachineDeliverAndQuery;
use App\Models\VendingMachine;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeliverProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(VendingMachine $vendingMachine, $ordinal, $orderId)
    {
        $this->vendingMachine = $vendingMachine;
        $this->ordianl = $ordinal;
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = app(VendingMachineDeliverAndQuery::class)->deliverProduct($this->vendingMachine->code, $this->orderId, $this->ordianl, $this->vendingMachine->cabinet_id, $this->vendingMachine->cabinet_type);
    }
}
