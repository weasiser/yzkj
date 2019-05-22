<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendingMachineAisle extends Model
{
    protected $fillable = [
        'ordinal', 'stock', 'max_stock', 'preferential_price'
    ];
    protected $casts = [
        'is_lead_rail' => 'boolean',
        'is_opened' => 'boolean'
    ];

    public function vending_machine()
    {
        return $this->belongsTo(VendingMachine::class);
    }
}
