<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAppSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // Seed default UPI settings
        $now = now();
        DB::table('app_settings')->insert([
            ['key' => 'upi_id',            'value' => '',                  'updated_by' => null, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'upi_amount',        'value' => '999',               'updated_by' => null, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'upi_plan_name',     'value' => 'Basic Plan',        'updated_by' => null, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'upi_business_name', 'value' => 'HotelHub',          'updated_by' => null, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'upi_note',          'value' => 'Monthly subscription', 'updated_by' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('app_settings');
    }
}
