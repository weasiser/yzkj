<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendingMachine extends Model
{
    protected $fillable = [
        'name', 'code', 'address', 'iot_card_no', 'is_opened', 'cabinet_id', 'cabinet_type'
    ];
    protected $casts = [
        'is_opened' => 'boolean'
    ];

    public function vendingMachineAisles()
    {
        return $this->hasMany(VendingMachineAisle::class)->orderBy('ordinal');
    }
}
