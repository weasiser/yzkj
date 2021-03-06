<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendingMachine extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
        'warehouse_id',
        'iot_card_no',
        'aisle_type',
        'machine_api_type',
        'is_opened',
        'cabinet_id',
        'cabinet_type',
        'is_delivering',
        'sold_count',
        'sold_value',
        'sold_profit',
    ];
    protected $casts = [
        'is_opened' => 'boolean',
        'is_delivering' => 'boolean',
    ];

    public function vendingMachineAisles()
    {
        return $this->hasMany(VendingMachineAisle::class)->orderBy('ordinal');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
