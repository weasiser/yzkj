<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('image');
            $table->unsignedDecimal('buying_price', 8, 2);
            $table->unsignedDecimal('selling_price', 8, 2);
            $table->unsignedTinyInteger('quality_guarantee_period');
            $table->unsignedSmallInteger('total_stock')->default(0);
            $table->unsignedInteger('sold_count')->default(0);
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
        Schema::dropIfExists('products');
    }
}
