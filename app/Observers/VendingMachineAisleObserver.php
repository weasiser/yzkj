<?php

namespace App\Observers;

use App\Models\VendingMachineAisle;

class VendingMachineAisleObserver
{
    /**
     * Handle the vending machine aisle "saved" event.
     *
     * @param  \App\Models\VendingMachineAisle  $vendingMachineAisle
     * @return void
     */
    public function saved(VendingMachineAisle $vendingMachineAisle)
    {
        $this->updateTotalVendingMachineStock($vendingMachineAisle);
    }

    /**
     * Handle the vending machine aisle "updated" event.
     *
     * @param  \App\Models\VendingMachineAisle  $vendingMachineAisle
     * @return void
     */
//    public function updated(VendingMachineAisle $vendingMachineAisle)
//    {
//        //
//    }

    /**
     * Handle the vending machine aisle "deleted" event.
     *
     * @param  \App\Models\VendingMachineAisle  $vendingMachineAisle
     * @return void
     */
    public function deleted(VendingMachineAisle $vendingMachineAisle)
    {
        $this->updateTotalVendingMachineStock($vendingMachineAisle);
    }

    protected function updateTotalVendingMachineStock($vendingMachineAisle)
    {
        $product = $vendingMachineAisle->product;
        $vendingMachineAisles = $product->vendingMachineAisles;
        $product->vending_machine_stock = $vendingMachineAisles->sum('stock');
        $product->save();
    }
}
