<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliverProductNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliver_product_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no')->unique();
            $table->string('code');
            $table->string('ordinal');
            $table->string('cabid');
            $table->string('cabtype');
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
        Schema::dropIfExists('deliver_product_notifications');
    }
}
