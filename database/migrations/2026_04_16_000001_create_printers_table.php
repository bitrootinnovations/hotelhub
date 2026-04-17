<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrintersTable extends Migration
{
    public function up()
    {
        Schema::create('printers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->string('mac_address', 50);
            $table->string('device_name', 100)->nullable();
            $table->enum('status', ['online', 'offline'])->default('offline');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('client_id')->on('client_masters')->onDelete('cascade');
            $table->unique(['client_id', 'mac_address']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('printers');
    }
}
