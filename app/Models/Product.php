<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'image', 'buying_price', 'selling_price', 'quality_guarantee_period', 'total_stock', 'sold_count', 'sold_value', 'sold_profit'
    ];
}
