<?php

namespace App\Transformers;

use App\Models\VendingMachine;
use League\Fractal\TransformerAbstract;

class VendingMachineTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['vendingMachineAisles', 'warehouse'];

    public function transform(VendingMachine $vendingMachine)
    {
        return [
            'id'           => $vendingMachine->id,
            'name'         => $vendingMachine->name,
            'code'         => $vendingMachine->code,
            'aisle_type'   => $vendingMachine->aisle_type,
            'machine_api_type' => $vendingMachine->machine_api_type,
            'cabinet_id'   => $vendingMachine->cabinet_id,
            'cabinet_type' => $vendingMachine->cabinet_type,
            'is_opened'    => $vendingMachine->is_opened,
            'warehouse_id' => $vendingMachine->warehouse_id,
//            'created_at' => (string) $vendingMachine->created_at,
//            'updated_at' => (string) $vendingMachine->updated_at,
        ];
    }

    public function includeVendingMachineAisles(VendingMachine $vendingMachine)
    {
        return $this->collection($vendingMachine->vendingMachineAisles, new VendingMachineAisleTransformer());
    }

    public function includeWarehouse(VendingMachine $vendingMachine)
    {
        return $this->item($vendingMachine->warehouse, new WarehouseTransformer());
    }
}
