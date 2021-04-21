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
        'market_price',
        'promotion_price',
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

    public function openedVendingMachineAisles()
    {
        return $this->hasMany(VendingMachineAisle::class)->leftJoin('vending_machines', 'vending_machine_aisles.vending_machine_id', '=', 'vending_machines.id')->where('vending_machine_aisles.is_opened', true)->where('vending_machines.is_opened', true)->select('vending_machine_aisles.stock');
    }
}
