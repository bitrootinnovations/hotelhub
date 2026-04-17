<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPasswordToClientMasters extends Migration
{
    public function up()
    {
        Schema::table('client_masters', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email_id')
                  ->comment('Mobile app login password for client');
            $table->index('email_id', 'cm_email_idx');
        });
    }

    public function down()
    {
        Schema::table('client_masters', function (Blueprint $table) {
            $table->dropIndex('cm_email_idx');
            $table->dropColumn('password');
        });
    }
}
