<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsSoldOutCheckedToVendingMachineAislesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vending_machine_aisles', function (Blueprint $table) {
            $table->boolean('is_sold_out_checked')->after('is_opened')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vending_machine_aisles', function (Blueprint $table) {
            $table->dropColumn('is_sold_out_checked');
        });
    }
}
