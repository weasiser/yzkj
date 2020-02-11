<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->index();
            $table->string('author')->nullable();
            $table->unsignedBigInteger('article_category_id')->nullable();
            $table->foreign('article_category_id')->references('id')->on('article_categories')->onDelete('set null');
            $table->text('body');
            $table->unsignedInteger('comment_count')->default(0)->index();
            $table->unsignedInteger('visit_count')->default(0)->index();
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
        Schema::dropIfExists('articles');
    }
}
