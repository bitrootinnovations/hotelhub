<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrinterTypeToPrintersTable extends Migration
{
    public function up()
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->string('printer_type', 50)->nullable()->after('device_name')
                  ->comment('e.g. Bluetooth, WiFi, USB');
        });
    }

    public function down()
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->dropColumn('printer_type');
        });
    }
}
