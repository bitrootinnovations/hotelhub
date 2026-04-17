<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('cart_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('table_id')->nullable();
            $table->string('user_ref', 50)->nullable()->comment('client_1 or employee_3 — who owns this cart');
            $table->enum('order_type', ['dine_in', 'takeaway'])->default('dine_in');
            $table->timestamps();

            $table->index(['client_id', 'user_ref'], 'carts_client_user_idx');
            $table->index('table_id', 'carts_table_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('carts');
    }
}
