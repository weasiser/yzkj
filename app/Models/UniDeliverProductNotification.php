<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniDeliverProductNotification extends Model
{
    protected $fillable = [
        'order_no',
        'machine_id',
        'aisle_number',
        'number',
        'extra',
        'result',
    ];

    protected $casts = [
        'extra' => 'array',
    ];
}
