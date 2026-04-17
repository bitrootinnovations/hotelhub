<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTypeMastersTable extends Migration
{
    public function up()
    {
        Schema::create('table_type_masters', function (Blueprint $table) {
            $table->bigIncrements('table_type_id');
            $table->string('type_name', 100); // Indoor, Outdoor, Rooftop
            $table->unsignedBigInteger('client_id')->nullable();
            $table->tinyInteger('status_id')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('client_id')->on('client_masters')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('table_type_masters');
    }
}
