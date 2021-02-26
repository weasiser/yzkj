<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYiputengDeliverProductNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yiputeng_deliver_product_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('trade_no')->unique();
            $table->string('machine_id');
            $table->string('shelf_id');
            $table->string('result');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yiputeng_deliver_product_notifications');
    }
}
