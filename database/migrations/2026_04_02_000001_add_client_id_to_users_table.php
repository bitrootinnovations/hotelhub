<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->after('role_id')
                  ->comment('NULL = platform-level user (admin/employee), set = belongs to a client');
            $table->index('client_id', 'users_client_id_index');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_client_id_index');
            $table->dropColumn('client_id');
        });
    }
}
