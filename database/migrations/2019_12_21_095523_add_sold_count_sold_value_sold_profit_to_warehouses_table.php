<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoldCountSoldValueSoldProfitToWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->unsignedInteger('sold_count')->default(0)->after('address');
            $table->unsignedDecimal('sold_value', 10, 2)->default(0)->after('sold_count');
            $table->unsignedDecimal('sold_profit', 10, 2)->default(0)->after('sold_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn('sold_count');
            $table->dropColumn('sold_value');
            $table->dropColumn('sold_profit');
        });
    }
}
