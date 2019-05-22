<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendingMachine extends Model
{
    protected $fillable = [
        'name', 'code', 'address', 'iot_card_no',
        'is_opened'
    ];
    protected $casts = [
        'is_opened' => 'boolean'
    ];
}
