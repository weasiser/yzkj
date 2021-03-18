<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMachineApiTypeToVendingMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vending_machines', function (Blueprint $table) {
            $table->unsignedSmallInteger('machine_api_type')->default(1)->after('aisle_type');
            $table->unsignedSmallInteger('cabinet_id')->nullable()->change();
            $table->unsignedSmallInteger('cabinet_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vending_machines', function (Blueprint $table) {
            $table->dropColumn('machine_api_type');
        });
    }
}
