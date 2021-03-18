<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YiputengDeliverProductNotification extends Model
{
    protected $fillable = [
        'trade_no',
        'machine_id',
        'shelf_id',
        'amount',
        'result'
    ];
}
