<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrintJobsTable extends Migration
{
    public function up()
    {
        Schema::create('print_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->string('type', 50)->default('upi_qr');   // upi_qr | receipt | etc.
            $table->json('payload');                           // all data needed to print
            $table->enum('status', ['pending', 'done', 'failed'])->default('pending');
            $table->timestamp('picked_at')->nullable();        // when mobile app fetched it
            $table->timestamp('done_at')->nullable();          // when mobile app confirmed done
            $table->timestamps();

            $table->foreign('client_id')->references('client_id')->on('client_masters')->onDelete('cascade');
            $table->index(['client_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('print_jobs');
    }
}
