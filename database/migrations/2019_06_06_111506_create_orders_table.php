<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no')->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->unsignedBigInteger('vending_machine_id');
            $table->foreign('vending_machine_id')->references('id')->on('vending_machines')->onDelete('set null');
            $table->unsignedBigInteger('aisle_id');
            $table->foreign('aisle_id')->references('id')->on('vending_machine_aisles')->onDelete('set null');
            $table->unsignedTinyInteger('amount');
            $table->decimal('sold_price', 8, 2);
            $table->decimal('total_amount', 8, 2);
            $table->text('remark')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_no')->nullable();
            $table->string('refund_status');
            $table->string('refund_no')->unique()->nullable();
            $table->boolean('is_closed')->default(false);
            $table->string('deliver_status');
            $table->text('deliver_data')->nullable();
            $table->text('extra')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
