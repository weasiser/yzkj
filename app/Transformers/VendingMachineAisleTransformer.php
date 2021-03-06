<?php

namespace App\Transformers;

use App\Models\VendingMachineAisle;
use League\Fractal\TransformerAbstract;

class VendingMachineAisleTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['product'];

    public function transform(VendingMachineAisle $vendingMachineAisle)
    {
        return [
            'id'                 => $vendingMachineAisle->id,
            'ordinal'            => $vendingMachineAisle->ordinal,
            'stock'              => $vendingMachineAisle->stock,
            'max_stock'          => $vendingMachineAisle->max_stock,
            'is_lead_rail'       => $vendingMachineAisle->is_lead_rail,
            'is_opened'          => $vendingMachineAisle->is_opened,
            'product_id'         => $vendingMachineAisle->product_id,
            'preferential_price' => (float)$vendingMachineAisle->preferential_price,
            'final_price'        => $vendingMachineAisle->product_id ? (float)big_number($vendingMachineAisle->product->selling_price)->subtract($vendingMachineAisle->product->promotion_price)->subtract($vendingMachineAisle->preferential_price)->getValue() : null,
//            'is_sold_out_checked' => $vendingMachineAisle->is_sold_out_checked,
//            'created_at' => (string) $vendingMachineAisle->created_at,
//            'updated_at' => (string) $vendingMachineAisle->updated_at,
        ];
    }

    public function includeProduct(VendingMachineAisle $vendingMachineAisle)
    {
        if ($vendingMachineAisle->product_id) {
            return $this->item($vendingMachineAisle->product, new ProductTransformer());
        }
    }
}
