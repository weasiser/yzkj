<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YipuengDeliverProductNotification extends Model
{
    protected $fillable = [
        'trade_no',
        'machine_id',
        'shelf_id',
        'result'
    ];
}
