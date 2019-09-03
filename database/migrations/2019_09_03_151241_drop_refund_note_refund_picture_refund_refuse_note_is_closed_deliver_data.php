<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropRefundNoteRefundPictureRefundRefuseNoteIsClosedDeliverData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('refund_note');
            $table->dropColumn('refund_picture');
            $table->dropColumn('refund_refuse_note');
            $table->dropColumn('is_closed');
            $table->dropColumn('deliver_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('refund_note')->nullable()->after('payment_no');
            $table->string('refund_picture')->nullable()->after('refund_note');
            $table->string('refund_refuse_note')->nullable()->after('refund_picture');
            $table->boolean('is_closed')->default(false)->after('refund_number');
            $table->json('deliver_data')->nullable()->after('deliver_status');
        });
    }
}
