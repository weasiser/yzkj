<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniDeliverProductNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_deliver_product_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_no')->unique();
            $table->string('machine_id');
            $table->string('aisle_number');
            $table->unsignedTinyInteger('number');
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
        Schema::dropIfExists('uni_deliver_product_notifications');
    }
}
