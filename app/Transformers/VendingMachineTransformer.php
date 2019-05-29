<?php

namespace App\Transformers;

use App\Models\VendingMachine;
use League\Fractal\TransformerAbstract;

class VendingMachineTransformer extends TransformerAbstract
{
    public function transform(VendingMachine $vendingMachine)
    {
        return [
            'id' => $vendingMachine->id,
            'name' => $vendingMachine->name,
            'code' => $vendingMachine->code,
            'cabinet_id' => $vendingMachine->cabinet_id,
            'cabinet_type' => $vendingMachine->cabinet_type,
            'is_opened' => $vendingMachine->is_opened,
//            'created_at' => (string) $vendingMachine->created_at,
//            'updated_at' => (string) $vendingMachine->updated_at,
        ];
    }
}
