<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAmountToYiputengDeliverProductNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yiputeng_deliver_product_notifications', function (Blueprint $table) {
            $table->unsignedSmallInteger('amount')->after('shelf_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yiputeng_deliver_product_notifications', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
}
