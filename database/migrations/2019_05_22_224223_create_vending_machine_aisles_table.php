<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendingMachineAislesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vending_machine_aisles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('ordinal');
            $table->unsignedTinyInteger('stock');
            $table->unsignedTinyInteger('max_stock');
            $table->decimal('preferential_price', 6, 2)->default(0);
            $table->boolean('is_lead_rail')->default(false);
            $table->boolean('is_opened')->default(true);
            $table->unsignedBigInteger('vending_machine_id');
            $table->foreign('vending_machine_id')->references('id')->on('vending_machines')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
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
        Schema::dropIfExists('vending_machine_aisles');
    }
}
