<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRegisteredStockAndIsSoldOutCheckedToProductPesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_pes', function (Blueprint $table) {
            $table->unsignedTinyInteger('registered_stock')->after('stock');
            $table->boolean('is_sold_out_checked')->after('registered_stock')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_pes', function (Blueprint $table) {
            $table->dropColumn('registered_stock');
            $table->dropColumn('is_sold_out_checked');
        });
    }
}
