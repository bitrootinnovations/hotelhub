<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('order_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('table_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->comment('Staff who placed the order');
            $table->string('order_number', 20)->unique();
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'served', 'cancelled'])->default('pending');
            $table->enum('order_type', ['dine_in', 'takeaway'])->default('dine_in');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('gst_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for fast queries
            $table->index('client_id',  'orders_client_id_index');
            $table->index('table_id',   'orders_table_id_index');
            $table->index('status',     'orders_status_index');
            $table->index('created_at', 'orders_created_at_index');
            $table->index(['client_id', 'status'], 'orders_client_status_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
