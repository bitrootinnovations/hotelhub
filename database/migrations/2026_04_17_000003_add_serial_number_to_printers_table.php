<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSerialNumberToPrintersTable extends Migration
{
    public function up()
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->string('serial_number', 100)->nullable()->after('printer_type')
                  ->comment('Device serial number from the printer label');
        });
    }

    public function down()
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->dropColumn('serial_number');
        });
    }
}
