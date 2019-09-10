<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title',
        'image',
        'buying_price',
        'selling_price',
        'quality_guarantee_period',
        'on_sale',
        'warehouse_stock',
        'vending_machine_stock',
        'total_stock',
        'total_registered_stock',
        'min_expiration_date',
        'sold_count',
        'sold_value',
        'sold_profit'
    ];

    public function productPes()
    {
        return $this->hasMany(ProductPes::class)->orderBy('production_date');
    }

    public function productPesWithoutSoldOutChecked()
    {
        return $this->hasMany(ProductPes::class)->orderBy('production_date')->where('is_sold_out_checked', false);
    }

//    public function getImageAttribute($value)
//    {
//        return $value . '?x-oss-process=style/webp';
//    }

    public function vendingMachineAisles()
    {
        return $this->hasMany(VendingMachineAisle::class);
    }
}
