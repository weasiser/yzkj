<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWarehouseStockVendingMachineStockToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->smallInteger('warehouse_stock')->default(0)->after('on_sale');
            $table->smallInteger('vending_machine_stock')->default(0)->after('warehouse_stock');
            $table->smallInteger('total_stock')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('warehouse_stock');
            $table->dropColumn('vending_machine_stock');
            $table->unsignedSmallInteger('total_stock')->change();
        });
    }
}
