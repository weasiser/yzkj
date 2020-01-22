<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPes extends Model
{
    protected $fillable = [
        'production_date',
        'expiration_date',
        'stock',
        'registered_stock',
        'is_sold_out_checked',
        'product_id',
        'warehouse_id',
    ];

    protected $casts = [
        'is_sold_out_checked' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
