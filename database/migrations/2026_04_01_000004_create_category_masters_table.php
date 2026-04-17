<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryMastersTable extends Migration
{
    public function up()
    {
        Schema::create('category_masters', function (Blueprint $table) {
            $table->bigIncrements('category_id');
            $table->string('category_name', 100);
            $table->unsignedBigInteger('client_id')->nullable();
            $table->tinyInteger('status_id')->default(1); // 1=Active 0=Inactive
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('client_id')->on('client_masters')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_masters');
    }
}
