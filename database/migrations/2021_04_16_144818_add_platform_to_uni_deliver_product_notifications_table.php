<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlatformToUniDeliverProductNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uni_deliver_product_notifications', function (Blueprint $table) {
            $table->json('extra')->after('number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uni_deliver_product_notifications', function (Blueprint $table) {
            $table->dropColumn('extra');
        });
    }
}
