<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpiIdToClientMasters extends Migration
{
    public function up()
    {
        Schema::table('client_masters', function (Blueprint $table) {
            $table->string('upi_id', 100)->nullable()->after('gst_number');
        });
    }

    public function down()
    {
        Schema::table('client_masters', function (Blueprint $table) {
            $table->dropColumn('upi_id');
        });
    }
}
