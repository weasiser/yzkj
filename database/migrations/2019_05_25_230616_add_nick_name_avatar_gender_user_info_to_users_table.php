<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNickNameAvatarGenderUserInfoToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nick_name')->nullable()->after('name');
            $table->string('avatar')->nullable()->after('nick_name');
            $table->string('gender')->nullable()->after('avatar');
            $table->json('user_info')->nullable()->after('gender');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nick_name');
            $table->dropColumn('avatar');
            $table->dropColumn('gender');
            $table->dropColumn('user_info');
        });
    }
}
