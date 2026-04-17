<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuMastersTable extends Migration
{
    public function up()
    {
        Schema::create('menu_masters', function (Blueprint $table) {
            $table->bigIncrements('menu_id');
            $table->string('menu_name', 150);
            $table->unsignedBigInteger('category_id');
            $table->tinyInteger('food_type');           // 1=Veg  2=Non-Veg
            $table->decimal('price', 10, 2);
            $table->decimal('gst_percentage', 5, 2)->default(0);
            $table->string('stock_type', 10);           // Unit | Kg
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->tinyInteger('status_id')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('category_id')->on('category_masters')->onDelete('restrict');
            $table->foreign('client_id')->references('client_id')->on('client_masters')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_masters');
    }
}
