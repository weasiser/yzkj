<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Warehouse extends Model
{
    protected $fillable = [
        'name',
        'address',
    ];

    public function vendingMachines()
    {
        return $this->hasMany(VendingMachine::class);
    }

    public function productPes()
    {
        return $this->hasMany(ProductPes::class);
    }

    public function productStock()
    {
        $vending_machine_stock = DB::table('product_pes')->leftJoin('vending_machine_aisles', function ($leftJoin) {
            $leftJoin->join('vending_machines', 'vending_machine_aisles.vending_machine_id', '=', 'vending_machines.id')->on('product_pes.product_id', '=', 'vending_machine_aisles.product_id')->on('product_pes.warehouse_id', '=', 'vending_machines.warehouse_id');
        })->selectRaw('sum(vending_machine_aisles.stock) as vending_machine_stock, product_pes.product_id')->groupBy('product_pes.product_id')->where('product_pes.warehouse_id', '=', $this->id);

        $total_stock = $this->hasMany(ProductPes::class)->selectRaw('sum(product_pes.stock) as total_stock, products.title, vending_machine_stock.vending_machine_stock, sum(product_pes.stock)-vending_machine_stock.vending_machine_stock as warehouse_stock')->join('products', 'product_pes.product_id', '=', 'products.id')->groupBy('product_pes.product_id')->leftJoinSub($vending_machine_stock, 'vending_machine_stock', function ($leftjoin) {
            $leftjoin->on('product_pes.product_id', '=', 'vending_machine_stock.product_id');
        });

        return $total_stock;
    }

    public function managers()
    {
        return $this->belongsToMany(User::class, 'warehouse_managers', 'warehouse_id', 'user_id')->withTimestamps();
    }
}
