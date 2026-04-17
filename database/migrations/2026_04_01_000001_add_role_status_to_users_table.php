<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleStatusToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->default(1)->after('id');
            $table->tinyInteger('status_id')->default(1)->after('remember_token'); // 1=Active 0=Inactive
            $table->foreign('role_id')->references('role_id')->on('role_masters')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'status_id']);
        });
    }
}
