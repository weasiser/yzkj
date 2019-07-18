<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliverProductNotification extends Model
{
    protected $fillable = [
        'no',
        'code',
        'ordinal',
        'cabid',
        'cabtype',
        'result'
    ];
}
