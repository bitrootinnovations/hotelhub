<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMastersTable extends Migration
{
    public function up()
    {
        Schema::create('table_masters', function (Blueprint $table) {
            $table->bigIncrements('table_id');
            $table->string('table_name', 50);           // T1, T2, A, B, 1, 2
            $table->unsignedBigInteger('table_type_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedTinyInteger('capacity')->nullable(); // seating capacity
            $table->tinyInteger('status_id')->default(1);        // 1=Available 0=Inactive
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('table_type_id')->references('table_type_id')->on('table_type_masters')->onDelete('restrict');
            $table->foreign('client_id')->references('client_id')->on('client_masters')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('table_masters');
    }
}
