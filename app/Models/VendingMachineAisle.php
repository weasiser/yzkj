<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendingMachineAisle extends Model
{
    protected $fillable = [
        'ordinal', 'stock', 'max_stock', 'preferential_price', 'product_id', 'is_lead_rail', 'is_opened', 'is_sold_out_checked'
    ];
    protected $casts = [
        'is_lead_rail' => 'boolean',
        'is_opened' => 'boolean',
        'is_sold_out_checked' => 'boolean'
    ];

    public function vendingMachine()
    {
        return $this->belongsTo(VendingMachine::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function decreaseStock($amount = 1)
    {
        $vendingMachineAisle = $this->where('id', $this->id)->where('stock', '>=', $amount)->first();
        if ($vendingMachineAisle) {
            $vendingMachineAisle->update(['stock' => $vendingMachineAisle->stock - $amount]);
        } else {
            throw new \Exception('该货道的商品库存不足');
        }
//        return $this->where('id', $this->id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    }

    public function decreaseStockAnyway($amount = 1)
    {
        $vendingMachineAisle = $this->where('id', $this->id)->first();
        if ($vendingMachineAisle->stock >= $amount) {
            $vendingMachineAisle->update(['stock' => $vendingMachineAisle->stock - $amount]);
        } else {
            $vendingMachineAisle->update(['stock' => 0]);
        }
    }

    public function increaseStock($amount = 1)
    {
        $vendingMachineAisle = $this->where('id', $this->id)->whereColumn('stock', '<', 'max_stock')->first();
        if ($vendingMachineAisle && ($vendingMachineAisle->stock + $amount <= $vendingMachineAisle->max_stock)) {
            $vendingMachineAisle->update(['stock' => $vendingMachineAisle->stock + $amount]);
        } else {
            throw new \Exception('该货道增加库存时超过最大库存');
        }
//        return $this->where('id', $this->id)->whereColumn('stock', '<', 'max_stock')->increment('stock', $amount);
    }
}
