<?php

namespace App\Transformers;

use App\Models\Warehouse;
use League\Fractal\TransformerAbstract;

class WarehouseTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['vendingMachines'];

    public function transform(Warehouse $warehouse)
    {
        return [
            'id'           => $warehouse->id,
            'name'         => $warehouse->name,
            'address'      => $warehouse->address,
//            'created_at' => (string) $vendingMachine->created_at,
//            'updated_at' => (string) $vendingMachine->updated_at,
        ];
    }

    public function includeVendingMachines(Warehouse $warehouse)
    {
        return $this->collection($warehouse->vendingMachines, new VendingMachineTransformer());
    }
}
