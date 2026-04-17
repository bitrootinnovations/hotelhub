<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_type', ['Cash', 'UPI', 'Card', 'Online', 'Other'])
                  ->nullable()->after('total_amount');
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])
                  ->default('pending')->after('payment_type');
            $table->timestamp('checked_out_at')->nullable()->after('payment_status');
            $table->index('payment_status', 'orders_payment_status_idx');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_payment_status_idx');
            $table->dropColumn(['payment_type', 'payment_status', 'checked_out_at']);
        });
    }
}
